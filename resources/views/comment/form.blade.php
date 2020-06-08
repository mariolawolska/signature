<div class="col-sm-12 col-md-5 " style="margin-bottom: 30px;background:  #BC9B78 "id="countPersonsBox"></div>
<div class="col-sm-12 col-md-1 "></div>
<div class="col-sm-12 col-md-6 form-fields " style="background:  #BC9B78;"><form  id="commentForm" action="" method="post">
     
        <div class="form-group">
            <label>NAME:</label>
            <input type="text" name="name" class="form-control"  value="{{Auth::user()->name}}" id="name" readonly="readonly" >
            @if ($errors->has('name'))
            <span class="text-danger">{{ $errors->first('name') }}</span>
            @endif
        </div>    
        {{-- Name END --}}            

        {{-- Email --}}
        <div class="form-group">
            <strong>EMAIL:</strong>
            <input type="text" name="email" class="form-control" value="{{Auth::user()->email}}" id="email" readonly="readonly" >
            @if ($errors->has('email'))
            <span class="text-danger">{{ $errors->first('email') }}</span>
            @endif
        </div>
        {{-- Email END --}}

        {{-- Comment --}}
        <div class="form-group mb-4">
            <label>COMMENT:</label></br>
            <textarea rows="5" cols="50" name="comment" placeholder="Add comment..." id="comment"></textarea>
        </div>
        
        {{-- Comment END --}}

        {{-- Submit --}}
        @include('comment.button.submit')
        {{-- Submit END --}}
        <div class="col-md-4 mt-4">
            <a  href="{{url('logout')}}">Logout</a>
        </div>
    </form>
</div>

