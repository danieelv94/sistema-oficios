<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight italic uppercase">
            {{ __('Muro de Avisos y Circulares - CEAA') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 shadow-sm" role="alert">
                    <p class="font-bold underline">Confirmado</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            @endif

            {{-- MURO DE PENDIENTES --}}
            <div class="grid grid-cols-1 gap-8">
                @forelse($avisos as $aviso)
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-8 {{ $aviso->prioridad == 'Urgente' ? 'border-red-600' : 'border-guinda-ceaa' }} transition hover:shadow-2xl">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="px-2 py-1 text-[10px] font-black uppercase rounded {{ $aviso->prioridad == 'Urgente' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                                        Circular {{ $aviso->prioridad }}
                                    </span>
                                    <h3 class="text-2xl font-black text-gray-900 mt-2 tracking-tight uppercase">{{ $aviso->titulo }}</h3>
                                    <p class="text-xs text-gray-400 font-bold uppercase mt-1">
                                        Publicado el {{ $aviso->created_at->format('d/m/Y H:i') }} por {{ $aviso->autor->name }}
                                    </p>
                                </div>
                            </div>

                            <div class="text-gray-700 leading-relaxed text-lg mb-6 whitespace-pre-line border-t border-gray-50 pt-4">
                                {{ $aviso->mensaje }}
                            </div>

                            @if($aviso->archivo)
                                <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-white rounded-full shadow-sm mr-4">
                                            @if(Str::endsWith($aviso->archivo, '.pdf'))
                                                <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg>
                                            @else
                                                <svg class="w-8 h-8 text-gris-oscuro" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"></path></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 uppercase">Documento Adjunto</p>
                                            <p class="text-xs text-gray-400 uppercase italic">Clic para abrir documento</p>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $aviso->archivo) }}" target="_blank" 
                                       class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-xs font-black rounded-md hover:bg-black transition uppercase tracking-widest">
                                        Ver Archivo
                                    </a>
                                </div>
                            @endif

                            <div class="flex justify-end border-t border-gray-50 pt-6">
                                <form action="{{ route('avisos.leer', $aviso) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-8 py-3 bg-guinda-ceaa text-white text-sm font-black rounded-full hover:opacity-90 transition transform hover:scale-105 shadow-md uppercase tracking-widest">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Marcar como Enterado
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-white rounded-xl shadow-inner border-2 border-dashed border-gray-200">
                        <p class="text-gray-400 font-bold uppercase italic text-xl">No hay circulares pendientes por leer.</p>
                    </div>
                @endforelse
            </div>

            {{-- HISTORIAL (Fuera del grid para mejor ancho) --}}
            @if($leidos->count() > 0)
                <div class="mt-16 border-t border-gray-200 pt-10">
                    <h3 class="text-lg font-bold text-gray-500 uppercase tracking-widest mb-6 italic">
                        {{ __('Circulares Consultadas Recientemente') }}
                    </h3>
                    
                    <div class="space-y-4">
                        @foreach($leidos as $leido)
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex justify-between items-center opacity-75 hover:opacity-100 transition">
                                <div class="flex items-center space-x-4">
                                    <div class="text-guinda-ceaa">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 uppercase text-sm">{{ $leido->titulo }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Leído el {{ \Carbon\Carbon::parse($leido->pivot->leido_at)->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex gap-4">
                                    @if($leido->archivo)
                                        <a href="{{ asset('storage/' . $leido->archivo) }}" target="_blank" class="text-[10px] font-black text-gris-oscuro hover:underline uppercase tracking-tighter">Descargar Anexo</a>
                                    @endif
                                    <button x-data x-on:click="alert('Contenido: {{ str_replace(["\r", "\n"], ' ', $leido->mensaje) }}')" class="text-[10px] font-black text-gray-400 hover:text-guinda-ceaa uppercase tracking-tighter">Ver Mensaje</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>