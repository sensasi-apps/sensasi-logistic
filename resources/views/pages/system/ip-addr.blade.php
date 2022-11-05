@extends('layouts.main')

@section('title', __('IP Addresses'))

@section('main-content')
    <div id="errorAlert" class="alert alert-danger d-none" role="alert">
        {{ __('Something went wrong, please refresh this page.' )}}
    </div>

    <div id="loading" class="text-center">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <div id="ipDisplayTemplate" class="section-body d-none">
        <h2 class="section-title"></h2>
        <p class="section-lead"></p>
    </div>

    <div id="ipList">

    </div>

    <div id="shareSection" class="d-none">
        <p class="m-0">Bagikan:</p>
        <div>
            <a id="waShareButton" class="btn btn-success btn-sm p-0 text-light" target="_blank">
                <i class="fab fa-whatsapp" style="font-size: 1.75rem; padding: .4rem"></i>
            </a>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/gh/joeymalvinni/webrtc-ip/dist/production.min.js"></script>
    <script>
        const loadingEl = document.getElementById('loading')

        getIPTypes().then(res => {
            const ipListEl = document.getElementById('ipList')
            const ipDisplayTemplateEl = document.getElementById('ipDisplayTemplate')
            const shareSectionEl = document.getElementById('shareSection')
            const waShareButtonEl = document.getElementById('waShareButton')

            let msg = '';

            res.map(ipObj => {
                const tmp = ipDisplayTemplateEl.cloneNode(true)
                tmp.removeAttribute('id')

                tmp.querySelector('.section-title').innerHTML = ipObj.ip
                tmp.querySelector('.section-lead').innerHTML = ipObj.type

                ipListEl.append(tmp)
                tmp.classList.remove('d-none');

                msg += ipObj.ip + "\n" + ipObj.type + "\n\n"
            })

            waShareButtonEl.href = 'https://wa.me?text=' + encodeURI(msg)
            shareSectionEl.classList.remove('d-none')
        }).catch(e => {
            errorAlert.classList.remove('d-none')
        }).finally(() => {
            loadingEl.remove();
        });
    </script>
@endpush
