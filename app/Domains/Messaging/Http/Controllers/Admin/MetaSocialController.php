<?php

namespace App\Domains\Messaging\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Messaging\Models\MetaSocialConnection;
use App\Domains\Messaging\Models\MetaSocialConversation;
use App\Domains\Messaging\Models\MetaSocialMessage;
use App\Domains\Messaging\Models\MetaSocialPage;
use App\Domains\Messaging\Services\MetaSocialGraphService;
use App\Domains\Messaging\Support\MetaSocialSettings;
use Illuminate\Support\Facades\Schema;

class MetaSocialController extends Controller
{
    public function __construct(
        private MetaSocialGraphService $graph,
    ) {}

    public function index()
    {
        $connectionMeta = $this->graph->connectionMeta();
        $connection = MetaSocialConnection::active();
        $tablesReady = Schema::hasTable('meta_social_pages');

        $stats = [
            'pages' => $tablesReady ? MetaSocialPage::where('is_active', true)->count() : 0,
            'conversations' => $tablesReady ? MetaSocialConversation::count() : 0,
            'messages_today' => $tablesReady
                ? MetaSocialMessage::whereDate('created_at', today())->count()
                : 0,
            'unread' => $tablesReady ? (int) MetaSocialConversation::sum('unread_count') : 0,
        ];

        $pages = $tablesReady
            ? MetaSocialPage::query()->orderBy('page_name')->limit(6)->get()
            : collect();

        return view('messaging::admin.meta-social.index', compact('connectionMeta', 'connection', 'stats', 'pages', 'tablesReady'));
    }
}
