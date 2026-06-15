@if(isset($avisoUrgente))
    <div x-data="{ showModal: true }" x-show="showModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center p-4" style="display: none;"
        x-init="$el.style.display = 'flex'">

        <div class="fixed inset-0 bg-gray-900 bg-opacity-85 backdrop-blur-sm transition-opacity"></div>

        {{-- Agregamos max-w-lg y mx-auto para que no se estire --}}
        <div class="relative w-full max-w-lg mx-auto z-[100000] transform transition-all shadow-2xl">
            <div class="bg-white rounded-xl overflow-hidden border-t-8 border-guinda-ceaa">

                <div class="p-5 border-b border-gray-100 flex items-center">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black uppercase tracking-tight text-gray-800">
                        Comunicado Urgente
                    </h3>
                </div>

                <div class="p-6">
                    <p class="text-lg font-black text-guinda-ceaa uppercase mb-3">
                        {{ $avisoUrgente->titulo }}
                    </p>
                    {{-- Fondo gris suave y scroll controlado --}}
                    <div
                        class="text-gray-600 leading-relaxed whitespace-pre-line bg-gray-50 p-4 rounded-lg border border-gray-200 max-h-60 overflow-y-auto text-sm shadow-inner">
                        {{ $avisoUrgente->mensaje }}
                    </div>

                    @if($avisoUrgente->archivo)
                        <div class="mt-5 p-3 bg-gris-claro/10 rounded-lg border border-gris-claro/20 flex items-center justify-between">
                            <span class="text-[10px] font-black text-gris-oscuro uppercase">Documento Oficial Adjunto</span>
                            <a href="{{ asset('storage/' . $avisoUrgente->archivo) }}" target="_blank"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-[10px] font-bold uppercase transition shadow-sm">
                                Ver Archivo
                            </a>
                        </div>
                    @endif
                </div>

                <div class="p-5 bg-gray-50 border-t border-gray-100 text-center">
                    <form action="{{ route('avisos.leer', $avisoUrgente) }}" method="POST">
                        @csrf
                        {{-- Botón con tu color guinda-ceaa --}}
                        <button type="submit"
                            class="w-full bg-guinda-ceaa hover:bg-opacity-90 text-white py-4 rounded-lg font-black uppercase tracking-widest text-sm shadow-lg transition transform active:scale-95">
                            Confirmar Lectura y Enterado
                        </button>
                    </form>
                    <p class="mt-3 text-[9px] text-gray-400 font-bold uppercase">
                        Debes confirmar para desbloquear las funciones del sistema.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif