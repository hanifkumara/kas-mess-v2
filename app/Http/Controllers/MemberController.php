<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $members = Member::query()
            ->when($request->string('q')->toString(), fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('members.index', ['members' => $members]);
    }

    public function create(): View
    {
        return view('members.form', ['member' => new Member()]);
    }

    public function store(MemberRequest $request): RedirectResponse
    {
        $member = Member::create($request->validated());

        return to_route('members.index')
            ->with('toast', ['type' => 'success', 'message' => "Anggota \"{$member->name}\" ditambahkan."]);
    }

    public function edit(Member $member): View
    {
        return view('members.form', ['member' => $member]);
    }

    public function update(MemberRequest $request, Member $member): RedirectResponse
    {
        $member->update($request->validated());

        return to_route('members.index')
            ->with('toast', ['type' => 'success', 'message' => "Data anggota \"{$member->name}\" diperbarui."]);
    }

    public function destroy(Member $member): RedirectResponse
    {
        $name = $member->name;
        $member->delete();

        return to_route('members.index')
            ->with('toast', ['type' => 'success', 'message' => "Anggota \"{$name}\" dihapus."]);
    }
}
