@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form method="post" action="{{route('LogIn')}}">
    @csrf
    @method('POST')
        <h3 class="text-center">Форма входа</h3>
        <div class="form-group">
            <input class="form-control item" type="text" name="username" id="username" placeholder="Логин" >
        </div>
        <div class="form-group">
            <input class="form-control item" type="password" name="password"  id="password" placeholder="Пароль">
        </div>

        <div class="form-group">
            <button class="btn btn-primary btn-block create-account" type="submit">Вход в аккаунт</button>
        </div>
    </form>
