<x-app-layout>
    <tasks-board :signed-in-user="{{ Js::from($signedInUser) }}" :statuses="{{ Js::from($statuses) }}"
        :priorities="{{ Js::from($priorities) }}" />
</x-app-layout>
