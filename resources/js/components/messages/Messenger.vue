<template>
    <main class="messenger-root d-flex flex-column h-100">
        <div v-if="!$root.conversation" class="empty-state flex-fill d-flex align-items-center justify-content-center text-center p-4 p-lg-5">
            <div class="empty-card mx-auto">
                <div class="empty-icon mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <h2 class="h3 mb-3">Choose a conversation</h2>
                <p class="text-muted mb-0">
                    Open any chat from the left panel to preview the full messenger experience.
                </p>
            </div>
        </div>

        <div v-else class="d-flex flex-column h-100">
            <ChatHeader :conversation="conversation" />
            <ChatContent :conversation="conversation" />
            <ChatFooter :conversation="conversation" />
        </div>
    </main>
</template>

<script>
import ChatHeader from "./ChatHeader.vue";
import ChatContent from "./ChatContent.vue";
import ChatFooter from "./ChatFooter.vue";

export default {
    components: {
        ChatHeader,
        ChatContent,
        ChatFooter,
    },
    props: {
        conversation: {
            type: Object,
            default: null,
        },
    },
};
</script>

<style scoped>
.messenger-root {
    min-height: 0;
}

.empty-card {
    max-width: 420px;
}

.empty-icon {
    width: 4.5rem;
    height: 4.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 1.5rem;
    color: #0284c7;
    background: rgba(14, 165, 233, 0.12);
}
</style>
