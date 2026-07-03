<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppConversationEvent extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'whatsapp_conversation_events';

    public const TYPE_CREATED = 'conversation_created';

    public const TYPE_MESSAGE_INBOUND = 'message_inbound';

    public const TYPE_MESSAGE_OUTBOUND = 'message_outbound';

    public const TYPE_TEMPLATE_SENT = 'template_sent';

    public const TYPE_ASSIGNED = 'assigned';

    public const TYPE_TRANSFERRED = 'transferred';

    public const TYPE_STATUS_CHANGED = 'status_changed';

    public const TYPE_NOTE_ADDED = 'note_added';

    public const TYPE_TAG_ADDED = 'tag_added';

    public const TYPE_TAG_REMOVED = 'tag_removed';

    public const TYPE_LEAD_LINKED = 'lead_linked';

    protected $fillable = [
        'conversation_id',
        'contact_id',
        'type',
        'title',
        'description',
        'meta',
        'performed_by',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(WhatsAppConversation::class, 'conversation_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(WhatsAppContact::class, 'contact_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
