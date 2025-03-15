<div class="form-group">
    <label>{{__("Room Name")}} <span class="text-danger">*</span></label>
    <input type="text" required value="{!! clean($translation->title) !!}" placeholder="{{__("Room name")}}" name="title" class="form-control">
</div>
<div class="form-group d-none">
    <label>{{__("Room Description")}}</label>
    <textarea name="content" cols="30" rows="5" class="form-control">{{$translation->content}}</textarea>
</div>
@if(is_default_lang())
    <div class="form-group">
        <label >{{__('Feature Image')}} </label>
        {!! \Modules\Media\Helpers\FileHelper::fieldUpload('image_id',$row->image_id) !!}
    </div>

    <div class="form-group">
        <label >{{__('Gallery')}}</label>
        {!! \Modules\Media\Helpers\FileHelper::fieldGalleryUpload('gallery',$row->gallery) !!}
    </div>
    <hr>
@endif
<input type="hidden" id="gallery-order" name="gallery_order" value="{{ $row->gallery }}">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var sortableGallery = document.getElementById('sortable-gallery');
        new Sortable(sortableGallery, {
            animation: 150,
            onEnd: function () {
                updateGalleryOrder();
            }
        });

        function updateGalleryOrder() {
            var order = [];
            document.querySelectorAll('#sortable-gallery .edit-img').forEach(function (item, index) {
                order.push(item.getAttribute('data-id'));
            });
            var orderString = order.join(',');
            document.getElementById('gallery-order').value = orderString;
        }
        document.querySelector('form').addEventListener('submit', function () {
            updateGalleryOrder();
        });
    });
</script>
