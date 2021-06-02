<?php
namespace App\Interfaces;

use Illuminate\Http\Request;

interface WebhookInterface
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(Request $request);

    /**
     * Undocumented function
     *
     * @return void
     */
    public function validate();

    /**
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function handle();
}
