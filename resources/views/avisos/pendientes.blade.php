<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    {{ __('Muro de Avisos y Circulares - CEAA') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Comunicados oficiales y circulares institucionales para su conocimiento y atención</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs font-semibold rounded-r-lg shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>
                        <span class="font-bold">Confirmado:</span> {{ session('success') }}
                    </div>
                </div>
            @endif

            {{-- MURO DE PENDIENTES --}}
            <div class="space-y-6">
                @forelse($avisos as $aviso)
                    <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden transition duration-200 hover:shadow-md">
                        {{-- Top color indicator strip --}}
                        <div class="h-1.5 {{ $aviso->prioridad == 'Urgente' ? 'bg-rose-500' : 'bg-guinda-ceaa' }}"></div>
                        
                        <div class="p-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4 pb-4 border-b border-slate-100">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-full tracking-wider {{ $aviso->prioridad == 'Urgente' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                            Circular {{ $aviso->prioridad }}
                                        </span>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-900 mt-2 tracking-tight uppercase">{{ $aviso->titulo }}</h3>
                                    <p class="text-xs text-slate-400 font-bold uppercase mt-0.5">
                                        Publicado el {{ $aviso->created_at->format('d/m/Y H:i') }} por {{ $aviso->autor->name }}
                                    </p>
                                </div>
                            </div>

                            <div class="text-slate-700 leading-relaxed text-sm mb-6 whitespace-pre-line">
                                {{ $aviso->mensaje }}
                            </div>

                            @if($aviso->archivo)
                                <div class="mb-6 p-4 bg-slate-50/80 rounded-xl border border-slate-200/80 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2.5 bg-white rounded-lg shadow-2xs border border-slate-200 text-rose-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-slate-800 uppercase tracking-wider">Documento Adjunto</p>
                                            <p class="text-[11px] text-slate-400">Clic para abrir o descargar documento oficial</p>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $aviso->archivo) }}" target="_blank" 
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-lg transition uppercase tracking-wider shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Ver Archivo
                                    </a>
                                </div>
                            @endif

                            <div class="flex justify-end pt-4 border-t border-slate-100">
                                <form action="{{ route('avisos.leer', $aviso) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white text-xs font-black rounded-lg transition shadow-xs hover:shadow-md uppercase tracking-wider">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Marcar como Enterado
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-white rounded-xl shadow-xs border border-slate-200/80">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-slate-600 font-bold uppercase text-sm">No hay circulares pendientes por leer.</p>
                        <p class="text-xs text-slate-400 mt-1">Te encuentras al día con todos los comunicados oficiales.</p>
                    </div>
                @endforelse
            </div>

            {{-- HISTORIAL DE CIRCULARES LEIDAS --}}
            @if($leidos->count() > 0)
                <div class="pt-8 border-t border-slate-200 space-y-4">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-wider">
                        {{ __('Circulares Consultadas Recientemente') }}
                    </h3>
                    
                    <div class="space-y-3">
                        @foreach($leidos as $leido)
                            <div class="bg-white p-4 rounded-xl shadow-xs border border-slate-200/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg border border-emerald-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 uppercase text-xs">{{ $leido->titulo }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">
                                            Leído el {{ \Carbon\Carbon::parse($leido->pivot->leido_at)->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-3 self-end sm:self-auto">
                                    @if($leido->archivo)
                                        <a href="{{ asset('storage/' . $leido->archivo) }}" target="_blank" class="text-xs font-bold text-slate-600 hover:text-guinda-ceaa uppercase tracking-wider flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Anexo
                                        </a>
                                    @endif
                                    <button x-data x-on:click="alert('Contenido:\n\n{{ addslashes(str_replace(["\r", "\n"], ' ', $leido->mensaje)) }}')" class="text-xs font-bold text-guinda-ceaa hover:underline uppercase tracking-wider">
                                        Ver Mensaje
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>