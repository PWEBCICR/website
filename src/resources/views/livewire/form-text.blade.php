<div>
    <label for="{{$form_elem}}" class="block font-medium text-md text-gray-700">
        {{$title}}</label>
    <input value="{{ old($form_elem, $previous ?? "")}}" type="text" name="{{$form_elem}}" id="{{$form_elem}}"
        class="form-input rounded-md shadow-sm mt-1 block w-full"
        placeholder="{{$placeHolder}}"/>
    <small id="{{$form_elem}}Help" class="block font-medium text-sm text-gray-500">
        {{$hint}}</small>
    @error($form_elem)
    <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
