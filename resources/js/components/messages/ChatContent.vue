<template>
    <div class="chat-content-shell flex-fill overflow-auto px-4 px-lg-5 py-4" id="chat-body">
        <div v-if="$root.requestError && !$root.loadingMessages" class="alert alert-warning d-flex align-items-center justify-content-between gap-3">
            <span>{{ $root.requestError }}</span>
            <button type="button" class="btn btn-sm btn-outline-secondary" @click="$root.retryCurrentView()">
                Retry
            </button>
        </div>

        <div v-if="$root.loadingMessages" class="text-muted small py-4">Loading messages...</div>

        <div v-else-if="!$root.messages.length" class="text-center text-muted py-5">
            No messages yet. This conversation is ready for your first demo message.
        </div>

        <div v-else class="d-flex flex-column gap-3">
            <article
                v-for="message in $root.messages"
                :key="message.id"
                class="message-row"
                :class="{ mine: Number(message.user_id) === $root.userId }"
            >
                <div class="message-bubble-wrap">
                    <div class="message-meta-strip">
                        <span class="message-author">
                            {{ message.user?.name || 'User' }}
                        </span>
                        <span class="message-time">{{ formatMessageTime(message.created_at) }}</span>
                    </div>

                    <div class="message-bubble">
                        <div v-if="message.type === 'attachment' && isImage(message)" class="message-attachment">
                            <button type="button" class="image-preview-trigger" @click="previewAttachment(message)">
                                <img class="img-fluid rounded-4 message-image" :src="fileUrl(message)" :alt="message.body?.file_name || 'Attachment'">
                            </button>
                        </div>

                        <div v-else-if="message.type === 'attachment'" class="attachment-card" :class="{ pdf: isPdf(message) }">
                            <div class="attachment-card-icon">
                                <span>{{ fileExtension(message) }}</span>
                            </div>
                            <div class="attachment-card-copy">
                                <div class="fw-semibold text-break">{{ message.body?.file_name }}</div>
                                <div class="small text-muted mb-3">{{ fileSize(message) }}</div>
                                <div class="attachment-card-actions">
                                    <button v-if="isPdf(message)" type="button" class="attachment-action-btn" @click="previewAttachment(message)">
                                        Preview
                                    </button>
                                    <a :href="fileUrl(message)" class="attachment-action-btn secondary" target="_blank" rel="noopener">
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>

                        <p v-else class="message-text mb-0">{{ message.body }}</p>
                    </div>

                    <div class="message-tools">
                        <button type="button" class="message-action-btn" @click="$root.deleteMessage(message, 'me')">
                            Hide For Me
                        </button>
                        <button
                            v-if="Number(message.user_id) === $root.userId"
                            type="button"
                            class="message-action-btn danger"
                            @click="$root.deleteMessage(message, 'all')"
                        >
                            Delete For Everyone
                        </button>
                    </div>
                </div>
            </article>
        </div>
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
    watch: {
        conversation: {
            immediate: true,
            handler(conversation) {
                if (conversation) {
                    this.$root.loadMessages(conversation);
                }
            },
        },
    },
    methods: {
        formatMessageTime(value) {
            return this.$root.moment(value).calendar();
        },
        isImage(message) {
            return Boolean(message.body?.mimetype && message.body.mimetype.match(/image\/.+/));
        },
        isPdf(message) {
            return message.body?.mimetype === "application/pdf";
        },
        fileUrl(message) {
            return `/storage/${message.body?.file_path || ''}`;
        },
        fileSize(message) {
            const size = Number(message.body?.file_size || 0) / 1024;
            return `${size.toFixed(2)} KB`;
        },
        fileExtension(message) {
            const fileName = message.body?.file_name || "FILE";
            if (!fileName.includes(".")) {
                return "FILE";
            }

            return fileName.split(".").pop().slice(0, 4).toUpperCase();
        },
        previewAttachment(message) {
            const kind = this.isImage(message) ? "image" : (this.isPdf(message) ? "pdf" : "file");
            if (kind === "file") {
                window.open(this.fileUrl(message), "_blank", "noopener");
                return;
            }

            this.$root.openMediaPreview({
                kind,
                url: this.fileUrl(message),
                name: message.body?.file_name || "Attachment",
                label: kind === "image" ? "Image preview" : "PDF preview",
            });
        },
    },
};
</script>

<style scoped>
.chat-content-shell {
    background: linear-gradient(180deg, rgba(248, 250, 252, 0.9), rgba(255, 255, 255, 0.98));
}

.message-row {
    display: flex;
    padding-bottom: 0.25rem;
}

.message-row.mine {
    justify-content: flex-end;
}

.message-bubble-wrap {
    max-width: min(78%, 720px);
}

.message-meta-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 0.55rem;
    padding-inline: 0.25rem;
}

.message-bubble {
    border-radius: 1.5rem;
    padding: 1.1rem 1.15rem;
    background: #ffffff;
    border: 1px solid rgba(148, 163, 184, 0.14);
    box-shadow: 0 14px 28px rgba(15, 23, 42, 0.06);
}

.message-row.mine .message-bubble {
    background: linear-gradient(135deg, #0ea5e9, #2563eb);
    color: #fff;
}

.message-author {
    color: #334155;
    font-size: 0.84rem;
    font-weight: 800;
    letter-spacing: 0.01em;
}

.message-row.mine .message-author,
.message-row.mine .message-time {
    color: #1d4ed8;
}

.message-text {
    white-space: pre-wrap;
    line-height: 1.8;
    font-size: 1rem;
    font-weight: 500;
    color: #0f172a;
}

.attachment-card {
    min-width: 290px;
    display: grid;
    grid-template-columns: 72px minmax(0, 1fr);
    gap: 0.95rem;
    align-items: center;
}

.attachment-card-icon {
    width: 72px;
    height: 72px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0ea5e9, #2563eb);
    color: #fff;
    font-size: 0.92rem;
    font-weight: 800;
    letter-spacing: 0.08em;
}

.attachment-card.pdf .attachment-card-icon {
    background: linear-gradient(135deg, #ef4444, #b91c1c);
}

.attachment-card-copy {
    min-width: 0;
}

.attachment-card-copy .text-muted {
    color: #64748b !important;
}

.attachment-card-actions {
    display: flex;
    gap: 0.55rem;
    flex-wrap: wrap;
}

.attachment-action-btn {
    border: 1px solid rgba(37, 99, 235, 0.18);
    background: rgba(219, 234, 254, 0.95);
    color: #1d4ed8;
    border-radius: 999px;
    padding: 0.45rem 0.8rem;
    font-size: 0.84rem;
    font-weight: 700;
    text-decoration: none;
}

.attachment-action-btn.secondary {
    background: #f8fafc;
    color: #0f172a;
    border-color: rgba(148, 163, 184, 0.18);
}

.message-tools {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    flex-wrap: wrap;
    margin-top: 0.75rem;
    padding: 0.45rem 0.55rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.96);
    border: 1px solid rgba(148, 163, 184, 0.18);
    box-shadow: 0 10px 18px rgba(15, 23, 42, 0.05);
}

.message-time {
    color: #64748b;
    font-weight: 700;
    font-size: 0.79rem;
}

.message-action-btn {
    border: 1px solid rgba(15, 23, 42, 0.08);
    background: #eef2ff;
    color: #1e3a8a;
    font-size: 0.8rem;
    font-weight: 800;
    padding: 0.48rem 0.82rem;
    border-radius: 999px;
    line-height: 1;
    transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
}

.message-action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 18px rgba(37, 99, 235, 0.12);
}

.message-action-btn.danger {
    border-color: rgba(220, 38, 38, 0.12);
    background: #fee2e2;
    color: #b91c1c;
}

.image-preview-trigger {
    border: none;
    background: transparent;
    padding: 0;
    display: block;
}

.message-image {
    max-width: min(520px, 100%);
    border-radius: 1.25rem !important;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
}

@media (max-width: 767.98px) {
    .message-bubble-wrap {
        max-width: 100%;
    }

    .message-meta-strip {
        align-items: flex-start;
        flex-direction: column;
        gap: 0.25rem;
    }

    .message-tools {
        width: 100%;
        border-radius: 1rem;
        justify-content: flex-start;
    }
}
</style>
