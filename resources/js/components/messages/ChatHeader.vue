<template>
    <div class="chat-header-shell border-bottom px-4 px-lg-5 py-4">
        <div class="d-flex align-items-center gap-3">
            <img :src="$root.conversationAvatar(conversation)" :alt="$root.conversationTitle(conversation)" class="header-avatar">

            <div class="min-w-0 flex-fill">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h2 class="h4 mb-0 text-truncate">{{ $root.conversationTitle(conversation) }}</h2>
                    <span class="badge rounded-pill bg-light text-secondary border">{{ conversation.type === 'group' ? 'Group chat' : 'Direct chat' }}</span>
                </div>
                <p class="text-muted mb-0 mt-1 small">{{ $root.conversationPresence(conversation) }}</p>
            </div>

            <div class="d-flex align-items-center gap-2 header-actions">
                <button type="button" class="header-action-btn" @click="$root.copyConversationLink(conversation)">
                    Copy link
                </button>
                <a :href="$root.conversationUrl(conversation)" class="header-action-btn">
                    Direct link
                </a>
            </div>
        </div>
        <p v-if="$root.copyStatus" class="small text-success mt-3 mb-0">{{ $root.copyStatus }}</p>
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
};
</script>

<style scoped>
.header-avatar {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 999px;
    object-fit: cover;
}

.chat-header-shell h2 {
    color: #0f172a;
    font-weight: 800;
    letter-spacing: -0.02em;
}

.chat-header-shell p {
    color: #64748b !important;
    font-size: 0.95rem;
}

.header-actions {
    flex-wrap: wrap;
    justify-content: flex-end;
}

.header-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 106px;
    padding: 0.68rem 1rem;
    border-radius: 0.95rem;
    border: 1px solid rgba(148, 163, 184, 0.22);
    background: #f8fafc;
    color: #0f172a;
    font-size: 0.9rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}

.header-action-btn:hover {
    border-color: rgba(14, 165, 233, 0.36);
    background: #eff6ff;
    color: #0369a1;
}
</style>
