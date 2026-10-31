@extends('sales::crm.layout')
@section('title',$document->title)
@section('content')
@include('sales::crm.partials.module-header',['title'=>$document->title,'subtitle'=>'Document','actions'=>[['label'=>'تحميل','url'=>$document->url(),'class'=>'crm-btn-primary','icon'=>'fas fa-download']]])
<section class="crm-card" style="padding:16px;max-width:720px">
<div class="crm-field"><span class="k">الملف</span><span class="v">{{ $document->original_name ?: basename($document->path) }}</span></div>
<div class="crm-field"><span class="k">التصنيف</span><span class="v">{{ $document->category?:'—' }}</span></div>
<div class="crm-field"><span class="k">النوع</span><span class="v">{{ $document->mime?:'—' }}</span></div>
<div class="crm-field"><span class="k">المسؤول</span><span class="v">{{ $document->assignee?->name }}</span></div>
<div class="crm-field"><span class="k">الوصف</span><span class="v">{{ $document->description?:'—' }}</span></div>
</section>
@endsection
