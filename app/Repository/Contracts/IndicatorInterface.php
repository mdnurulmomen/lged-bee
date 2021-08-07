<?php

namespace App\Repository\Contracts;

use Illuminate\Http\Request;

interface IndicatorInterface
{
    public function index();
    public function show(Request $request);
    public function store(Request $request);
    public function update(Request $request);
    public function destroy(Request $request);
}
