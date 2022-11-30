@section('title','Edit '.$person->full_name)
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit : <strong>{{$person->full_name}}</strong>
        </h2>
    </x-slot>

    <div>
        <div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="block mb-8">
                <a href="{{ route('person.index')  }}"
                   class="bg-gray-200 hover:bg-gray-300 text-black font-bold py-2 px-4 rounded">Back</a>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form method="post" action="{{ route('person.update', $person) }}">
                    @csrf
                    @method('PUT')
                    <div class="shadow overflow-hidden sm:rounded-md">
                        @foreach($fields as $field)
                            @php
                                $refugee_detail[$field->id] = isset($refugee_detail[$field->id])
                                ? $refugee_detail[$field->id]
                                : "";

                            @endphp
                            <div class="px-4 py-4 bg-white sm:p-6">
                                <label for="{{$field->label}}"
                                       class="block font-medium text-sm text-gray-700">{{$field->title}}</label>
                                @if($field->linked_list != "")
                                    @php
                                        $list=$field->getLinkedListContent();
                                        $selected = $refugee_detail[$field->label];
                                    @endphp
                                    @livewire("select-dropdown", ['label' => $field->id, 'placeholder' => "-- Select
                                    your ".$field->title." --", 'datas' => $list, "selected_value" =>
                                    $selected])
                                    @stack('scripts')
                                @else
                                    @livewire("forms.form", [
                                    'form_elem' => $field->id,
                                    'type' => $field->dataType->html_type,
                                    'placeHolder' => $field->placeholder ?? '',
                                    'previous' => $refugee_detail[$field->id],
                                    'showError' => false
                                    ])
                                @endif
                                @error($field->id)
                                <p class="text-sm text-red-600">{{ Str::replace($field->id, $field->title, $message) }}</p>
                                @enderror
                            </div>
                        @endforeach
                        <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <button
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent
                                rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700
                                active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray
                                disabled:opacity-25 transition ease-in-out duration-150">
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
