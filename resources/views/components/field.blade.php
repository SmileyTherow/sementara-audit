@props(['name','rules'=>'nullable|string','value'=>null,'options'=>null,'label'=>null])
@php
$id=$name.'-'.\Illuminate\Support\Str::random(6);
$value=old($name,$value); $required=str_starts_with($rules,'required|');
$isFile=str_contains($rules,'|image'); $isBool=str_contains($rules,'boolean');
$type=$isFile?'file':(str_contains($rules,'|date')?'date':(str_contains($rules,'numeric')||str_contains($rules,'integer')?'number':($name==='password'||str_contains($name,'password')?'password':(str_contains($rules,'|email')?'email':'text'))));
if (!$options && preg_match('/(?:^|\|)in:([^|]+)/',$rules,$match)) $options=array_combine(explode(',',$match[1]),explode(',',$match[1]));
$long=in_array($name,['description','content','notes','excerpt','address','cooperative_address','site_description','general_notes','treatment','fertilizer_notes','pest_notes','disease_notes','status_reason','whatsapp_note']);
@endphp
<div class="field {{ $long?'field-wide':'' }}"><label for="{{ $id }}">{{ $label ?? \App\Support\Resources::label($name) }} @if($required)<span class="required">*</span>@endif</label>
@if($isBool)<select id="{{ $id }}" name="{{ $name }}"><option value="1" @selected((string)$value==='1')>Ya</option><option value="0" @selected(!$value)>Tidak</option></select>
@elseif($options!==null)<select id="{{ $id }}" name="{{ $name }}" @required($required)><option value="">Pilih {{ strtolower($label ?? \App\Support\Resources::label($name)) }}</option>@foreach($options as $key=>$text)<option value="{{ $key }}" @selected((string)$value===(string)$key)>{{ $text }}</option>@endforeach</select>
@elseif($long)<textarea id="{{ $id }}" name="{{ $name }}" rows="4" @required($required)>{{ $value }}</textarea>
@else<input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" @if(!$isFile) value="{{ $value instanceof \DateTimeInterface?$value->format('Y-m-d'):($type==='password'?'':$value) }}" @else accept="image/jpeg,image/png,image/webp" @endif @if($type==='number') step="{{ str_contains($rules,'integer')?'1':'any' }}" min="{{ $name==='latitude'?-90:($name==='longitude'?-180:0) }}" @endif @required($required)>
@endif
@if($isFile)<small>JPG, PNG, WebP · 4 MB maksimum</small>@if($value)<img class="field-preview" src="{{ Storage::url($value) }}" alt="Foto saat ini">@endif @endif
@error($name)<small class="field-error">{{ $message }}</small>@enderror</div>
