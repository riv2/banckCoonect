<div class="row">
    @foreach($item->material_files as $file)
    <div class="col-6 col-md-4 col-lg-2 mt-3">
        <a href="/{{ $file }}" class="d-block h-100 p-2 border" download>
            @php $ext = \File::extension($file); @endphp
            <figure class="d-flex flex-column h-100">
                @if(is_file($file) && in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif']))
                    <img src="/{{ $file }}" alt="" class="img-fluid d-block m-auto" style="max-height: 200px">
                    <figcaption class="text-secondary text-center text-decoration-none small d-block mt-auto mb-0">
                        @php $getimagesize = getimagesize($file); @endphp
                        {{ $ext }}, {{ $getimagesize[0] }}x{{ $getimagesize[1] }}px
                    </figcaption>
                @else
                    <img src="https://gocpa.cloud/images/network-logo.png" alt="" class="img-fluid d-block m-auto">
                    @if($ext == 'pdf')
                        <figcaption class="text-secondary text-center text-decoration-none small d-block mt-auto mb-0">{{ __('Document PDF') }}</figcaption>
                    @elseif($ext == 'doc' || $ext ==  'docx' )
                        <figcaption class="text-secondary text-center text-decoration-none small d-block mt-auto mb-0">{{ __('Document MS Word') }}</figcaption>
                    @else
                        <figcaption class="text-secondary text-center text-decoration-none small d-block mt-auto mb-0">{{ __('Document') }}</figcaption>
                    @endif
                @endif
            </figure>
        </a>
    </div>
    @endforeach
</div>

{{--
<div class="row">
   <a href="{{ route("partner.offers.materials.download") }}?id={{ $item->offer_material_id }}">
       <button type="button" class="btn btn-primary">{{ __('partners.offers.materials.banner.get_archive') }}</button>
   </a>
</div> --}}
