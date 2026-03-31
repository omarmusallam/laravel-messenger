import { createApp } from "vue";
import Messenger from "./components/messages/Messenger.vue";
import ChatList from "./components/messages/ChatList.vue";
import Echo from "laravel-echo";

window.Pusher = require("pusher-js");

const config = window.messengerConfig || {};

const chatApp = createApp({
    data() {
        return {
            conversations: [],
            conversation: null,
            messages: [],
            userId: Number(config.userId || 0),
            csrfToken: config.csrfToken || "",
            laravelEcho: null,
            chatChannel: null,
            selectedConversationId: config.selectedConversationId ? Number(config.selectedConversationId) : null,
            alertAudio: new Audio("/assets/mixkit-correct-answer-tone-2870.wav"),
            loadingConversations: false,
            loadingMessages: false,
            requestError: null,
            conversationSearch: "",
            composerError: null,
            copyStatus: "",
            previewMedia: null,
        };
    },
    mounted() {
        this.alertAudio.addEventListener("ended", () => {
            this.alertAudio.currentTime = 0;
        });

        this.loadConversations();
        this.setupEcho();
    },
    methods: {
        moment(time) {
            return window.moment(time);
        },
        conversationUrl(conversation) {
            return `${config.routes?.messenger || "/messenger"}/${conversation.id}`;
        },
        participantFor(conversation) {
            if (!conversation || !Array.isArray(conversation.participants)) {
                return null;
            }

            return conversation.participants.find((participant) => Number(participant.id) !== this.userId) || conversation.participants[0] || null;
        },
        conversationTitle(conversation) {
            if (!conversation) {
                return "Conversation";
            }

            if (conversation.type === "group" && conversation.label) {
                return conversation.label;
            }

            return this.participantFor(conversation)?.name || "Conversation";
        },
        conversationAvatar(conversation) {
            return this.participantFor(conversation)?.avatar_url || "https://ui-avatars.com/api/?background=0F172A&color=fff&name=Chat";
        },
        conversationPresence(conversation) {
            if (conversation?.type === "group") {
                return `${conversation.participants?.length || 0} participants`;
            }

            const participant = this.participantFor(conversation);
            if (!participant) {
                return "Conversation";
            }

            if (participant.isTyping) {
                return "Typing...";
            }

            return participant.isOnline ? "Online" : "Available for demo";
        },
        conversationPreview(conversation) {
            if (!conversation?.last_message) {
                return "No messages yet";
            }

            if (conversation.last_message.type === "attachment") {
                return conversation.last_message.body?.file_name || "Attachment";
            }

            return conversation.last_message.body || "No messages yet";
        },
        conversationMatchesQuery(conversation) {
            const query = this.conversationSearch.trim().toLowerCase();

            if (!query) {
                return true;
            }

            return [
                this.conversationTitle(conversation),
                conversation.label,
                this.conversationPreview(conversation),
            ]
                .filter(Boolean)
                .some((value) => String(value).toLowerCase().includes(query));
        },
        applyConversationDefaults(conversation) {
            if (!conversation?.participants) {
                conversation.participants = [];
            }

            conversation.participants = conversation.participants.map((participant) => ({
                isOnline: false,
                isTyping: false,
                ...participant,
            }));

            if (typeof conversation.new_messages === "undefined") {
                conversation.new_messages = 0;
            }

            return conversation;
        },
        async loadConversations() {
            this.loadingConversations = true;
            this.requestError = null;

            try {
                const response = await fetch(config.routes?.conversations || "/api/conversations", {
                    headers: {
                        Accept: "application/json",
                    },
                });

                if (!response.ok) {
                    throw new Error(`Unable to load conversations: ${response.status}`);
                }

                const payload = await response.json();
                this.conversations = (payload.data || []).map((conversation) => this.applyConversationDefaults(conversation));

                if (this.selectedConversationId) {
                    const selected = this.conversations.find((conversation) => Number(conversation.id) === this.selectedConversationId);
                    if (selected) {
                        this.selectConversation(selected);
                        return;
                    }
                }

                if (this.conversations.length > 0) {
                    this.selectConversation(this.conversations[0]);
                }
            } catch (error) {
                console.error(error);
                this.requestError = "Unable to load conversations right now.";
                this.conversations = [];
            } finally {
                this.loadingConversations = false;
            }
        },
        async loadMessages(conversation) {
            if (!conversation) {
                this.messages = [];
                return;
            }

            this.loadingMessages = true;
            this.requestError = null;

            try {
                const response = await fetch(`${config.routes?.conversations || "/api/conversations"}/${conversation.id}/messages`, {
                    headers: {
                        Accept: "application/json",
                    },
                });

                if (!response.ok) {
                    throw new Error(`Unable to load messages: ${response.status}`);
                }

                const payload = await response.json();
                this.messages = (payload.messages?.data || []).reverse();
                this.scrollMessagesToEnd();
            } catch (error) {
                console.error(error);
                this.requestError = "Unable to load messages right now.";
                this.messages = [];
            } finally {
                this.loadingMessages = false;
            }
        },
        async selectConversation(conversation) {
            if (!conversation) {
                return;
            }

            this.requestError = null;
            this.composerError = null;
            this.conversation = conversation;
            this.selectedConversationId = Number(conversation.id);
            window.history.replaceState({}, "", this.conversationUrl(conversation));
            this.markAsRead(conversation);
        },
        async copyConversationLink(conversation) {
            if (!conversation) {
                return;
            }

            const url = `${window.location.origin}${this.conversationUrl(conversation)}`;

            try {
                await navigator.clipboard.writeText(url);
                this.copyStatus = "Link copied";
            } catch (error) {
                console.error(error);
                this.copyStatus = "Copy failed";
            }

            setTimeout(() => {
                this.copyStatus = "";
            }, 1800);
        },
        retryCurrentView() {
            if (this.conversation) {
                this.loadMessages(this.conversation);
                return;
            }

            this.loadConversations();
        },
        openMediaPreview(payload) {
            this.previewMedia = payload;
            document.body.classList.add("overflow-hidden");
        },
        closeMediaPreview() {
            this.previewMedia = null;
            document.body.classList.remove("overflow-hidden");
        },
        scrollMessagesToEnd() {
            requestAnimationFrame(() => {
                const container = document.querySelector("#chat-body");
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },
        async markAsRead(conversation = null) {
            const activeConversation = conversation || this.conversation;
            if (!activeConversation) {
                return;
            }

            try {
                const response = await fetch(`${config.routes?.conversations || "/api/conversations"}/${activeConversation.id}/read`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": this.csrfToken,
                    },
                    body: JSON.stringify({
                        _token: this.csrfToken,
                    }),
                });

                if (response.ok) {
                    activeConversation.new_messages = 0;
                }
            } catch (error) {
                console.error(error);
            }
        },
        async deleteMessage(message, target) {
            try {
                const response = await fetch(`${config.routes?.messages || "/api/messages"}/${message.id}`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": this.csrfToken,
                    },
                    body: JSON.stringify({
                        target,
                        _token: this.csrfToken,
                    }),
                });

                if (!response.ok) {
                    throw new Error(`Delete failed: ${response.status}`);
                }

                if (target === "me") {
                    this.messages = this.messages.filter((current) => current.id !== message.id);
                    return;
                }

                message.body = "Message deleted";
                message.type = "text";
            } catch (error) {
                console.error(error);
                this.composerError = "Unable to update the message right now.";
            }
        },
        findUser(id, conversationId) {
            const conversation = this.conversations.find((item) => Number(item.id) === Number(conversationId));
            if (!conversation) {
                return null;
            }

            return conversation.participants.find((participant) => Number(participant.id) === Number(id)) || null;
        },
        async refreshConversation(conversationId) {
            try {
                const response = await fetch(`${config.routes?.conversations || "/api/conversations"}/${conversationId}`, {
                    headers: {
                        Accept: "application/json",
                    },
                });

                if (!response.ok) {
                    return;
                }

                const conversation = this.applyConversationDefaults(await response.json());
                this.conversations.unshift(conversation);
            } catch (error) {
                console.error(error);
            }
        },
        updateConversationFromIncoming(message) {
            const index = this.conversations.findIndex((conversation) => Number(conversation.id) === Number(message.conversation_id));

            if (index === -1) {
                this.refreshConversation(message.conversation_id);
                return;
            }

            const conversation = this.conversations[index];
            conversation.last_message = message;
            conversation.new_messages = Number(conversation.new_messages || 0) + 1;
            this.conversations.splice(index, 1);
            this.conversations.unshift(conversation);

            if (this.conversation && Number(this.conversation.id) === Number(message.conversation_id)) {
                this.messages.push(message);
                conversation.new_messages = 0;
                this.scrollMessagesToEnd();
            }
        },
        setupEcho() {
            const key = config.echo?.key;
            const cluster = config.echo?.cluster;

            if (!key || !cluster || !window.Pusher) {
                return;
            }

            this.laravelEcho = new Echo({
                broadcaster: "pusher",
                key,
                cluster,
                forceTLS: true,
            });

            this.laravelEcho
                .join(`Messenger.${this.userId}`)
                .listen(".new-message", (data) => {
                    this.updateConversationFromIncoming(data.message);
                    this.alertAudio.play().catch(() => null);
                });

            this.chatChannel = this.laravelEcho
                .join("Chat")
                .joining((user) => {
                    this.conversations.forEach((conversation) => {
                        const participant = conversation.participants.find((item) => Number(item.id) === Number(user.id));
                        if (participant) {
                            participant.isOnline = true;
                        }
                    });
                })
                .leaving((user) => {
                    this.conversations.forEach((conversation) => {
                        const participant = conversation.participants.find((item) => Number(item.id) === Number(user.id));
                        if (participant) {
                            participant.isOnline = false;
                        }
                    });
                })
                .listenForWhisper("typing", (event) => {
                    const user = this.findUser(event.id, event.conversation_id);
                    if (user) {
                        user.isTyping = true;
                    }
                })
                .listenForWhisper("stopped-typing", (event) => {
                    const user = this.findUser(event.id, event.conversation_id);
                    if (user) {
                        user.isTyping = false;
                    }
                });
        },
    },
});

chatApp.component("ChatList", ChatList);
chatApp.component("Messenger", Messenger);
chatApp.mount("#chat-app");
