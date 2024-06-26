@extends ('forum::layouts.main', ['thread' => null, 'breadcrumbs_append' => [$thread->title], 'thread_title' => $thread->title])

@section ('content')
    <div id="thread">
        <div class="flex flex-col md:flex-row justify-between my-4">
            <h2 class="grow text-3xl font-semibold">{{ $thread->title }}</h2>

            <div class="flex flex-col md:flex-row items-center gap-2">
                @if (Gate::allows('deleteThreads', $thread->category) && Gate::allows('delete', $thread))
                    @if ($thread->trashed())
                        <x-forum::button-link href="#" class="bg-red-500 hover:bg-red-400" data-open-modal="perma-delete-thread">
                            <i data-feather="trash"></i> {{ trans('forum::general.perma_delete') }}
                        </x-forum::button-link>
                    @else
                        <x-forum::button-link href="#" class="bg-red-500 hover:bg-red-400 inline-flex items-center gap-2" data-open-modal="delete-thread">
                            <i data-feather="trash" class="w-4"></i> {{ trans('forum::general.delete') }}
                        </x-forum::button-link>
                    @endif
                @endif
                @if ($thread->trashed() && Gate::allows('restoreThreads', $thread->category) && Gate::allows('restore', $thread))
                    <x-forum::button-link href="#" data-open-modal="restore-thread" class="inline-flex items-center gap-2">
                        <i data-feather="refresh-cw" class="w-4"></i> {{ trans('forum::general.restore') }}
                    </x-forum::button-link>
                @endif

                @if (Gate::allows('lockThreads', $category)
                    || Gate::allows('pinThreads', $category)
                    || Gate::allows('rename', $thread)
                    || Gate::allows('moveThreadsFrom', $category))
                    <x-forum::button-group>
                        @if (!$thread->trashed())
                            @can ('lockThreads', $category)
                                @if ($thread->locked)
                                    <x-forum::button-link href="#" data-open-modal="unlock-thread" class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-400">
                                        <i data-feather="unlock" class="w-4"></i> {{ trans('forum::threads.unlock') }}
                                    </x-forum::button-link>
                                @else
                                    <x-forum::button-link href="#" data-open-modal="lock-thread" class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-400">
                                        <i data-feather="lock" class="w-4"></i> {{ trans('forum::threads.lock') }}
                                    </x-forum::button-link>
                                @endif
                            @endcan
                            @can ('pinThreads', $category)
                                @if ($thread->pinned)
                                    <x-forum::button-link href="#" data-open-modal="unpin-thread" class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-400">
                                        <i data-feather="arrow-down"></i> {{ trans('forum::threads.unpin') }}
                                    </x-forum::button-link>
                                @else
                                    <x-forum::button-link href="#" data-open-modal="pin-thread" class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-400">
                                        <i data-feather="arrow-up" class="w-4"></i> {{ trans('forum::threads.pin') }}
                                    </x-forum::button-link>
                                @endif
                            @endcan
                            @can ('rename', $thread)
                                <x-forum::button-link href="#"  data-open-modal="rename-thread" class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-400">
                                    <i data-feather="edit-2" class="w-4"></i> {{ trans('forum::general.rename') }}
                                </x-forum::button-link>
                            @endcan
                            @can ('moveThreadsFrom', $category)
                                <x-forum::button-link href="#" data-open-modal="move-thread" class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-400">
                                    <i data-feather="corner-up-right" class="w-4"></i> {{ trans('forum::general.move') }}
                                </x-forum::button-link>
                            @endcan
                        @endif
                    </x-forum::button-group>
                @endcan
            </div>
        </div>

        <div class="flex flex-wrap gap-x-1">
            @if ($thread->trashed())
                <x-forum::badge type="danger">{{ trans('forum::general.deleted') }}</x-forum::badge>
            @endif
            @if ($thread->pinned)
                <x-forum::badge type="info">{{ trans('forum::threads.pinned') }}</x-forum::badge>
            @endif
            @if ($thread->locked)
                <x-forum::badge type="warning">{{ trans('forum::threads.locked') }}</x-forum::badge>
            @endif
        </div>

        @if ((count($posts) > 1 || $posts->currentPage() > 1) && (Gate::allows('deletePosts', $thread) || Gate::allows('restorePosts', $thread)) && count($selectablePosts) > 0)
            <form :action="postActions[state.selectedPostAction]" method="POST">
                @csrf
                <input type="hidden" name="_method" :value="postActionMethods[state.selectedPostAction]" />
        @endif

        <div class="my-4">
            <div>
                {{ $posts->links('forum::pagination') }}
            </div>
            <div class="flex justify-end">
                @if (!$thread->trashed())
                    @can ('reply', $thread)
                        <x-forum::button-group>
                            <x-forum::button-link href="{{ Forum::route('post.create', $thread) }}">
                                {{ trans('forum::general.new_reply') }}
                            </x-forum::button-link>
                            <x-forum::button-link href="#quick-reply">
                                {{ trans('forum::general.quick_reply') }}
                            </x-forum::button-link>
                        </x-forum::button-group>
                    @endcan
                @endif
            </div>
        </div>

        @if ((count($posts) > 1 || $posts->currentPage() > 1) && (Gate::allows('deletePosts', $thread) || Gate::allows('restorePosts', $thread)) && count($selectablePosts) > 0)
            <div class="text-end mb-2">
                <div class="form-check">
                    <label for="selectAllPosts">
                        {{ trans('forum::posts.select_all') }}
                    </label>
                    <input type="checkbox" value="" id="selectAllPosts" class="align-middle" @click="toggleAll" :checked="state.selectedPosts.length == posts.data.length">
                </div>
            </div>
        @endif

        @foreach ($posts as $post)
            @include ('forum::post.partials.list', compact('post'))
        @endforeach

        @if ((count($posts) > 1 || $posts->currentPage() > 1) && (Gate::allows('deletePosts', $thread) || Gate::allows('restorePosts', $thread)) && count($selectablePosts) > 0)
                <div class="fixed bottom-0 right-0 m-2" style="z-index: 1000;" v-if="state.selectedPosts.length">
                    <div class="bg-white shadow-sm rounded-md min-w-96 max-w-full">
                        <div class="border-b text-center py-4 px-6">
                            {{ trans('forum::general.with_selection') }}
                        </div>
                        <div class="p-6">
                            <div class="mb-3">
                                <div>
                                    <x-forum::label for="bulk-actions">{{ trans_choice('forum::general.actions', 1) }}</x-forum::label>
                                </div>

                                <x-forum::select id="bulk-actions" v-model="state.selectedPostAction">
                                    <option value="delete">{{ trans('forum::general.delete') }}</option>
                                    <option value="restore">{{ trans('forum::general.restore') }}</option>
                                </x-forum::select>
                            </div>

                            @if (config('forum.general.soft_deletes'))
                                <div class="form-check mb-3" v-if="state.selectedPostAction == 'delete'">
                                    <input class="form-check-input" type="checkbox" name="permadelete" value="1" id="permadelete">
                                    <label class="form-check-label" for="permadelete">
                                        {{ trans('forum::general.perma_delete') }}
                                    </label>
                                </div>
                            @endif

                            <div class="text-end">
                                <x-forum::button type="submit" class="px-5" @click="submitPosts">{{ trans('forum::general.proceed') }}</x-forum::button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif

        {{ $posts->links('forum::pagination') }}

        @if (!$thread->trashed())
            @can ('reply', $thread)
                <h2>{{ trans('forum::general.quick_reply') }}</h2>
                <div id="quick-reply" class="mt-4">
                    <form method="POST" action="{{ Forum::route('post.store', $thread) }}">
                        @csrf

                        <div class="mb-3">
                            <x-forum::textarea name="content" class="w-full min-h-48">{{ old('content') }}</x-forum::textarea>
                        </div>

                        <div class="text-end">
                            <x-forum::button type="submit" class="px-5">{{ trans('forum::general.reply') }}</x-forum::button>
                        </div>
                    </form>
                </div>
            @endcan
        @endif
    </div>

    @if ($thread->trashed() && Gate::allows('restoreThreads', $thread->category) && Gate::allows('restore', $thread))
        @component('forum::modal-form')
            @slot('key', 'restore-thread')
            @slot('title', '<i data-feather="refresh-cw" class="text-gray-500"></i>' . trans('forum::general.restore'))
            @slot('route', Forum::route('thread.restore', $thread))
            @slot('method', 'POST')

            {{ trans('forum::general.generic_confirm') }}

            @slot('actions')
                <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
            @endslot
        @endcomponent
    @endif

    @if (Gate::allows('deleteThreads', $thread->category) && Gate::allows('delete', $thread))
        @component('forum::modal-form')
            @slot('key', 'delete-thread')
            @slot('title', '<i data-feather="trash" class="text-gray-500"></i>' . trans('forum::threads.delete'))
            @slot('route', Forum::route('thread.delete', $thread))
            @slot('method', 'DELETE')

            @if (config('forum.general.soft_deletes'))
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="permadelete" value="1" id="permadelete">
                    <label class="form-check-label" for="permadelete">
                        {{ trans('forum::general.perma_delete') }}
                    </label>
                </div>
            @else
                {{ trans('forum::general.generic_confirm') }}
            @endif

            @slot('actions')
                <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
            @endslot
        @endcomponent

        @if (config('forum.general.soft_deletes'))
            @component('forum::modal-form')
                @slot('key', 'perma-delete-thread')
                @slot('title', '<i data-feather="trash" class="text-gray-500"></i>' . trans_choice('forum::threads.perma_delete', 1))
                @slot('route', Forum::route('thread.delete', $thread))
                @slot('method', 'DELETE')

                <input type="hidden" name="permadelete" value="1" />

                {{ trans('forum::general.generic_confirm') }}

                @slot('actions')
                    <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                @endslot
            @endcomponent
        @endif
    @endif

    @if (!$thread->trashed())
        @can ('lockThreads', $category)
            @if ($thread->locked)
                @component('forum::modal-form')
                    @slot('key', 'unlock-thread')
                    @slot('title', '<i data-feather="unlock" class="text-gray-500"></i> ' . trans('forum::threads.unlock'))
                    @slot('route', Forum::route('thread.unlock', $thread))
                    @slot('method', 'POST')

                    {{ trans('forum::general.generic_confirm') }}

                    @slot('actions')
                        <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                    @endslot
                @endcomponent
            @else
                @component('forum::modal-form')
                    @slot('key', 'lock-thread')
                    @slot('title', '<i data-feather="lock" class="text-gray-500"></i> ' . trans('forum::threads.lock'))
                    @slot('route', Forum::route('thread.lock', $thread))
                    @slot('method', 'POST')

                    {{ trans('forum::general.generic_confirm') }}

                    @slot('actions')
                        <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                    @endslot
                @endcomponent
            @endif
        @endcan

        @can ('pinThreads', $category)
            @if ($thread->pinned)
                @component('forum::modal-form')
                    @slot('key', 'unpin-thread')
                    @slot('title', '<i data-feather="arrow-down" class="text-gray-500"></i> ' . trans('forum::threads.unpin'))
                    @slot('route', Forum::route('thread.unpin', $thread))
                    @slot('method', 'POST')

                    {{ trans('forum::general.generic_confirm') }}

                    @slot('actions')
                        <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                    @endslot
                @endcomponent
            @else
                @component('forum::modal-form')
                    @slot('key', 'pin-thread')
                    @slot('title', '<i data-feather="arrow-up" class="text-gray-500"></i> ' . trans('forum::threads.pin'))
                    @slot('route', Forum::route('thread.pin', $thread))
                    @slot('method', 'POST')

                    {{ trans('forum::general.generic_confirm') }}

                    @slot('actions')
                        <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                    @endslot
                @endcomponent
            @endif
        @endcan

        @can ('rename', $thread)
            @component('forum::modal-form')
                @slot('key', 'rename-thread')
                @slot('title', '<i data-feather="edit-2" class="text-gray-500"></i> ' . trans('forum::general.rename'))
                @slot('route', Forum::route('thread.rename', $thread))
                @slot('method', 'POST')

                <div>
                    <x-forum::label for="new-title">{{ trans('forum::general.title') }}</x-forum::label>
                    <x-forum::input type="text" name="title" value="{{ $thread->title }}" class="w-full" />
                </div>

                @slot('actions')
                    <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                @endslot
            @endcomponent
        @endcan

        @can ('moveThreadsFrom', $category)
            @component('forum::modal-form')
                @slot('key', 'move-thread')
                @slot('title', '<i data-feather="corner-up-right" class="text-gray-500"></i> ' . trans('forum::general.move'))
                @slot('route', Forum::route('thread.move', $thread))
                @slot('method', 'POST')

                <div class="input-group">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="category-id">{{ trans_choice('forum::categories.category', 1) }}</label>
                    </div>
                    <select name="category_id" id="category-id" class="form-select">
                        @include ('forum::category.partials.options', ['hide' => $thread->category])
                    </select>
                </div>

                @slot('actions')
                    <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                @endslot
            @endcomponent
        @endcan
    @endif

    <script type="module">
    Vue.createApp({
        setup() {
            let posts = @json($posts);
            posts.data = posts.data.filter(post => post.sequence > 1);

            const selectablePosts = @json($selectablePosts);
            const postActions = {
                delete: "{{ Forum::route('bulk.post.delete') }}",
                restore: "{{ Forum::route('bulk.post.restore') }}"
            };
            const postActionMethods = {
                delete: 'DELETE',
                restore: 'POST',
            };

            const state = Vue.reactive({
                selectedPostAction: 'delete',
                selectedPosts: [],
                selectedThreadAction: null,
            });

            function toggleAll() {
                state.selectedPosts = (state.selectedPosts.length < selectablePosts.length) ? selectablePosts : [];
            }

            function submitThread(event) {
                if (threadActionMethods[state.selectedThreadAction] === 'DELETE' && !confirm("{{ trans('forum::general.generic_confirm') }}"))
                {
                    event.preventDefault();
                }
            }

            function submitPosts(event) {
                if (postActionMethods[state.selectedPostAction] === 'DELETE' && !confirm("{{ trans('forum::general.generic_confirm') }}")) {
                    event.preventDefault();
                }
            }

            return {
                posts,
                selectablePosts,
                postActions,
                postActionMethods,
                state,
                toggleAll,
                submitThread,
                submitPosts,
            };
        }
    }).mount('#thread');
    </script>
@stop
