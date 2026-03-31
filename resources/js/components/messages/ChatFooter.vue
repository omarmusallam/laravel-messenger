<template>
    <div class="chat-footer-shell border-top px-4 px-lg-5 py-4 bg-white">
        <form class="d-flex flex-column gap-3" @submit.prevent="sendMessage">
            <div v-if="$root.composerError" class="alert alert-warning mb-0 py-2">
                {{ $root.composerError }}
            </div>

            <div v-if="attachments.length" class="attachment-preview-shell">
                <div class="attachment-preview-toolbar">
                    <div>
                        <p class="attachment-preview-title mb-1">Selected attachments</p>
                        <p class="attachment-preview-subtitle mb-0">{{ attachments.length }} file(s) ready before sending</p>
                    </div>
                    <button type="button" class="btn attachment-clear-btn" @click="clearAttachments">
                        Clear all
                    </button>
                </div>

                <div class="attachment-preview-grid">
                    <div v-for="attachment in attachments" :key="attachment.id" class="attachment-preview-card">
                        <div v-if="attachment.isImage" class="attachment-image-wrap">
                            <img :src="attachment.previewUrl" :alt="attachment.name" class="attachment-image-preview">
                        </div>

                        <div v-else class="attachment-file-icon">
                            <span>{{ attachment.extension }}</span>
                        </div>

                        <div class="attachment-copy">
                            <p class="attachment-label mb-1">Ready to send</p>
                            <p class="attachment-name mb-1">{{ attachment.name }}</p>
                            <p class="attachment-meta mb-0">{{ attachment.sizeLabel }}</p>
                        </div>

                        <button type="button" class="btn btn-sm attachment-remove-btn" @click="removeAttachment(attachment.id)">
                            Remove
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 align-items-end">
                <button type="button" class="btn composer-attach-btn" @click="selectFile">
                    Attach
                </button>

                <div class="flex-fill">
                    <label for="message-box" class="form-label small text-muted mb-2">Message</label>
                    <textarea
                        id="message-box"
                        v-model="message"
                        class="form-control"
                        rows="2"
                        placeholder="Type your message..."
                        @focus="$root.markAsRead()"
                        @input="startTyping"
                        @keydown.enter.exact.prevent="sendMessage"
                    ></textarea>
                </div>

                <button type="submit" class="btn composer-send-btn px-4" :disabled="sending || (!message.trim() && !attachments.length)">
                    {{ sending ? 'Sending...' : 'Send' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    props: {
        conversation: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            message: "",
            attachments: [],
            startTypingActive: false,
            timeout: null,
            sending: false,
        };
    },
    computed: {
    },
    methods: {
        startTyping() {
            if (!this.$root.chatChannel || !this.$root.conversation) {
                return;
            }

            if (!this.startTypingActive) {
                this.startTypingActive = true;
                this.$root.chatChannel.whisper("typing", {
                    id: this.$root.userId,
                    conversation_id: this.$root.conversation.id,
                });
            }

            if (this.timeout) {
                clearTimeout(this.timeout);
            }

            this.timeout = setTimeout(() => {
                this.startTypingActive = false;
                this.$root.chatChannel.whisper("stopped-typing", {
                    id: this.$root.userId,
                    conversation_id: this.$root.conversation.id,
                });
            }, 800);
        },
        async sendMessage() {
            if ((!this.message.trim() && !this.attachments.length) || !this.$root.conversation || this.sending) {
                return;
            }

            this.sending = true;
            this.$root.composerError = null;

            try {
                const textMessage = this.message.trim();
                const uploads = [...this.attachments];

                if (textMessage) {
                    await this.sendSinglePayload({
                        message: textMessage,
                    });
                    this.message = "";
                }

                for (const attachment of uploads) {
                    await this.sendSinglePayload({
                        message: "",
                        attachment: attachment.file,
                    });
                    this.removeAttachment(attachment.id);
                }
            } catch (error) {
                console.error(error);
                this.$root.composerError = error.message || "Unable to send the message right now.";
            } finally {
                this.sending = false;
            }
        },
        async sendSinglePayload({ message = "", attachment = null }) {
            const formData = new FormData();
            formData.append("conversation_id", this.$root.conversation.id);
            formData.append("message", message);
            formData.append("_token", this.$root.csrfToken);

            if (attachment) {
                formData.append("attachment", attachment);
            }

            const response = await fetch(window.messengerConfig?.routes?.messages || "/api/messages", {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": this.$root.csrfToken,
                },
                body: formData,
            });

            if (!response.ok) {
                const payload = await response.json().catch(() => ({}));
                throw new Error(payload.message || `Send failed: ${response.status}`);
            }

            const payload = await response.json();
            this.$root.messages.push(payload);
            this.$root.conversation.last_message = payload;
            this.$root.scrollMessagesToEnd();
        },
        buildAttachmentEntry(file) {
            const kilobytes = file.size / 1024;
            const sizeLabel = kilobytes < 1024
                ? `${kilobytes.toFixed(1)} KB`
                : `${(kilobytes / 1024).toFixed(2)} MB`;
            const extension = file.name.includes(".")
                ? file.name.split(".").pop().slice(0, 4).toUpperCase()
                : "FILE";

            return {
                id: `${file.name}-${file.size}-${file.lastModified}-${Math.random().toString(36).slice(2, 8)}`,
                file,
                name: file.name,
                extension,
                isImage: Boolean(file.type && file.type.startsWith("image/")),
                sizeLabel,
                previewUrl: URL.createObjectURL(file),
            };
        },
        removeAttachment(attachmentId) {
            const attachment = this.attachments.find((item) => item.id === attachmentId);
            if (attachment?.previewUrl) {
                URL.revokeObjectURL(attachment.previewUrl);
            }

            this.attachments = this.attachments.filter((item) => item.id !== attachmentId);
        },
        clearAttachments() {
            this.attachments.forEach((attachment) => {
                if (attachment.previewUrl) {
                    URL.revokeObjectURL(attachment.previewUrl);
                }
            });

            this.attachments = [];
        },
        appendFiles(files) {
            const nextAttachments = files.map((file) => this.buildAttachmentEntry(file));
            this.attachments = [...this.attachments, ...nextAttachments];
        },
        selectFile() {
            const input = document.createElement("input");
            input.type = "file";
            input.multiple = true;

            input.addEventListener("change", () => {
                if (input.files.length > 0) {
                    this.appendFiles(Array.from(input.files));
                }
            });

            input.click();
        },
    },
    beforeUnmount() {
        this.clearAttachments();
    },
};
</script>

<style scoped>
.chat-footer-shell {
    background: rgba(255, 255, 255, 0.98) !important;
}

.chat-footer-shell .form-label {
    color: #475569 !important;
    font-size: 0.88rem;
    font-weight: 700;
}

.chat-footer-shell .form-control {
    min-height: 3rem;
    border-radius: 1rem;
    border-color: rgba(148, 163, 184, 0.24);
    background: #f8fafc;
    color: #0f172a;
    font-size: 0.98rem;
}

.attachment-preview-shell {
    padding: 0;
}

.attachment-preview-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 0.9rem;
    padding: 0.15rem 0.1rem 0;
}

.attachment-preview-title {
    color: #0f172a;
    font-size: 0.96rem;
    font-weight: 800;
}

.attachment-preview-subtitle {
    color: #64748b;
    font-size: 0.84rem;
    font-weight: 600;
}

.attachment-clear-btn {
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, 0.22);
    background: #fff;
    color: #334155;
    font-weight: 700;
}

.attachment-preview-grid {
    display: grid;
    gap: 0.85rem;
}

.attachment-preview-card {
    display: grid;
    grid-template-columns: 80px minmax(0, 1fr) auto;
    gap: 1rem;
    align-items: center;
    border-radius: 1.25rem;
    border: 1px solid rgba(148, 163, 184, 0.2);
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    padding: 0.9rem 1rem;
}

.attachment-image-wrap {
    width: 80px;
    height: 80px;
    border-radius: 1rem;
    overflow: hidden;
    background: #e2e8f0;
}

.attachment-image-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.attachment-file-icon {
    width: 80px;
    height: 80px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0ea5e9, #2563eb);
    color: #fff;
    font-weight: 800;
    letter-spacing: 0.08em;
}

.attachment-label {
    color: #64748b;
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.attachment-name {
    color: #0f172a;
    font-size: 1rem;
    font-weight: 700;
    word-break: break-word;
}

.attachment-meta {
    color: #64748b;
    font-size: 0.88rem;
}

.attachment-remove-btn {
    border-radius: 999px;
    border: 1px solid rgba(239, 68, 68, 0.2);
    background: rgba(254, 226, 226, 0.9);
    color: #b91c1c;
    font-weight: 700;
}

.composer-attach-btn {
    min-width: 88px;
    border-radius: 1rem;
    border: 1px solid rgba(14, 165, 233, 0.2);
    background: #eff6ff;
    color: #0369a1;
    font-weight: 700;
}

.composer-send-btn {
    min-width: 88px;
    border-radius: 1rem;
    background: linear-gradient(135deg, #0ea5e9, #2563eb);
    border: none;
    color: #fff;
    font-weight: 700;
    box-shadow: 0 14px 28px rgba(37, 99, 235, 0.18);
}

.composer-send-btn:disabled,
.composer-attach-btn:disabled {
    opacity: 0.65;
}

@media (max-width: 767.98px) {
    .attachment-preview-toolbar {
        align-items: flex-start;
        flex-direction: column;
    }

    .attachment-preview-card {
        grid-template-columns: 1fr;
    }
}
</style>
