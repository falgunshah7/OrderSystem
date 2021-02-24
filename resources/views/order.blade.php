<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>

    <!-- Styles -->
    <style>
    </style>

    <style>
        body {
            font-family: 'Nunito';
        }

        .error{
            color: red;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center mt-5">
            <div class="panel panel-default m-auto">
                <div class="panel-heading"><h2>Order System</h2></div>
                <div class="panel-body">
                    <form id="formSubmit">
                        @csrf
                        <div class="form-group">
                            <label for="exampleInputPassword1">Row Number</label>
                            <input type="number" class="form-control" id="row_number" name="row_number" placeholder="Row Number">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Column Number</label>
                            <input type="number" class="form-control" id="column_number" name="column_number" placeholder="Column Number">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
            <div class="col-12 justify-content-center align-items-center mt-5" id="item_data">
            </div>
        </div>
    </div>
    {{--Modal Add Item--}}
        <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="itemOrderForm">
                        <div class="modal-body">

                            <div class="form-group">
                                <label for="exampleInputPassword1">Item Name</label>
                                <input type="text" class="form-control" id="item_name" name="name" placeholder="Item Name">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Price</label>
                                <input type="number" class="form-control" id="price" name="price" placeholder="Price">
                            </div>
                        </div>
                        <input type="hidden" name="row" id="item_row" value="" />
                        <input type="hidden" name="col" id="item_col" value="" />
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Make Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    {{--Modal End--}}
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script type="text/javascript">
        var form = $("#formSubmit").validate({
            rules:{
                row_number: {
                    required: true,
                    number: true
                },
                column_number: {
                    required: true,
                    number: true
                }
            },
            messages: {
                row_number: 'Please enter valid row number',
                column_number: 'Please enter valid column number',
            }
        });

        $("#formSubmit").submit(function (e) {
            e.preventDefault();
            if(form.valid()) {
                $.ajax({
                    url: '{{ route('getGrid') }}',
                    type: 'POST',
                    data: $(this).serializeArray(),
                    success: function (res) {
                        $("#item_data").html(res.data);
                    }
                })
            }
        });

        var positionArr = new Array();

        function crateOrder(e) {
            $('#orderModal').find('input').val('');
            $("#orderModal").modal('show');

            $("#item_row").val($(e).attr('data-row'));
            $("#item_col").val($(e).attr('data-col'));

            if (typeof $(e).data('name') !== 'undefined') {
                $("#item_name").val($(e).attr('data-name'));
                $("#price").val($(e).attr('data-price'));
            }
        }

        var itemOrderForm = $("#itemOrderForm").validate({
            rules:{
                name: 'required',
                price: {
                    required: true,
                    number: true
                }
            },
            messages: {
                row_number: 'Please enter item name',
                column_number: 'Please enter price',
            }
        });

        $("#itemOrderForm").submit(function (e) {
            e.preventDefault();

            if(itemOrderForm.valid()) {
                $.ajax({
                    url: '{{ route('store') }}',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: $(this).serializeArray(),
                    success: function (res) {
                        console.log(res);
                        if(res.status == 'success'){
                            $('.btn-primary[data-row="'+res.data.item_row+'"][data-col="'+res.data.item_column+'"]').html(res.data.name);
                            $('.btn-primary[data-row="'+res.data.item_row+'"][data-col="'+res.data.item_column+'"]').attr('data-name',res.data.name);
                            $('.btn-primary[data-row="'+res.data.item_row+'"][data-col="'+res.data.item_column+'"]').attr('data-price',res.data.price);
                            $('#orderModal').find('input').val('');
                            $('#orderModal').modal('toggle');
                        }
                    }
                })
            }
        });

    </script>
</body>
</html>
