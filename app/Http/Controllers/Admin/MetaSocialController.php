<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaSocialConnection;
use App\Models\MetaSocialConversation;
use App\Models\MetaSocialMessage;
use App\Models\MetaSocialPage;
use App\Services\MetaSocial\MetaSocialGraphService;
use App\Support\MetaSocialSettings;
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

        return view('admin.meta-social.index', compact('connectionMeta', 'connection', 'stats', 'pages', 'tablesReady'));
    }
}
