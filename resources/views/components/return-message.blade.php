<dialog class="pink-border open modal">
    <form method="dialog">
        <x-nav.exit-button type="button" formmethod="dialog" />
        <x-logo.small />
        <p>
            @if($type == "succes")
                Nicely done! <br>
            @endif
            {!! $message !!}
        </p>
        <button class="action-button">
            Thanks for letting me know
        </button>
    </form>
</dialog>