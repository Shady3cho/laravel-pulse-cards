<?php

namespace LangtonMwanza\PulseDevops\Http\Controllers;

use Illuminate\Routing\Controller;
use LangtonMwanza\PulseDevops\Services\DatabaseInspector;

class DatabaseController extends Controller
{
    public function __construct(
        protected DatabaseInspector $databaseInspector,
    ) {}

    public function index()
    {
        if (! config('pulse-devops.cards.database_tables', true)) {
            abort(404);
        }

        $tables = $this->databaseInspector->getTables();
        $size = $this->databaseInspector->getDatabaseSize();

        return view('pulse-devops::pages.database', compact('tables', 'size'));
    }
}
