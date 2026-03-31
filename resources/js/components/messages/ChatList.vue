<template>
    <div class="chat-list-shell flex-fill overflow-auto px-4 pb-4">
        <div v-if="$root.requestError" class="alert alert-warning mt-3 mb-0">
            {{ $root.requestError }}
        </div>

        <div v-if="$root.loadingConversations" class="text-muted small py-4">
            Loading conversations...
        </div>

        <div v-else-if="!$root.conversations.length" class="empty-conversations py-4 text-muted">
            No conversations are available yet.
        </div>

        <div v-else-if="!filteredConversations.length" class="empty-conversations py-4 text-muted">
            No conversations match your search.
        </div>

        <a
            v-for="conversation in filteredConversations"
            :key="conversation.id"
            :href="$root.conversationUrl(conversation)"
            class="conversation-card text-reset text-decoration-none"
            :class="{ active: $root.conversation && $root.conversation.id === conversation.id }"
            @click.prevent="setConversation(conversation)"
        >
            <img :src="$root.conversationAvatar(conversation)" :alt="$root.conversationTitle(conversation)" class="conversation-avatar">

            <div class="conversation-copy min-w-0">
                <div class="d-flex align-items-center gap-3 mb-1">
                    <h3 class="conversation-title mb-0 text-truncate">{{ $root.conversationTitle(conversation) }}</h3>
                    <span v-if="conversation.last_message?.created_at" class="conversation-time text-muted small ms-auto">
                        {{ $root.moment(conversation.last_message.created_at).fromNow() }}
                    </span>
                </div>

                <p class="conversation-presence text-muted small mb-1">
                    {{ $root.conversationPresence(conversation) }}
                </p>

                <div class="d-flex align-items-center gap-3">
                    <p class="conversation-preview mb-0 text-truncate">
                        {{ $root.conversationPreview(conversation) }}
                    </p>
                    <span v-if="conversation.new_messages" class="badge rounded-pill bg-primary ms-auto">
                        {{ conversation.new_messages }}
                    </span>
                </div>
            </div>
        </a>
    </div>
</template>

<script>
export default {
    computed: {
        filteredConversations() {
            return this.$root.conversations.filter((conversation) => this.$root.conversationMatchesQuery(conversation));
        },
    },
    methods: {
        setConversation(conversation) {
            this.$root.selectConversation(conversation);
        },
    },
};
</script>

<style scoped>
.chat-list-shell {
    min-height: 0;
}

.conversation-card {
    display: grid;
    grid-template-columns: 56px minmax(0, 1fr);
    gap: 0.95rem;
    align-items: center;
    padding: 1rem;
    margin-top: 0.75rem;
    border: 1px solid rgba(148, 163, 184, 0.16);
    border-radius: 1.25rem;
    background: #ffffff;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
}

.conversation-card:hover,
.conversation-card.active {
    transform: translateY(-1px);
    border-color: rgba(14, 165, 233, 0.36);
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    box-shadow: 0 20px 36px rgba(15, 23, 42, 0.08);
}

.conversation-avatar {
    width: 56px;
    height: 56px;
    border-radius: 999px;
    object-fit: cover;
}

.conversation-title {
    font-size: 1.02rem;
    font-weight: 800;
    color: #0f172a;
}

.conversation-time,
.conversation-presence {
    color: #64748b !important;
}

.conversation-preview {
    color: #334155;
    font-size: 0.96rem;
    line-height: 1.55;
}
</style>
