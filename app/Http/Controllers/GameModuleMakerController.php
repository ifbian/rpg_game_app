<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\GameModule;
use App\Models\GameModuleData;

class GameModuleMakerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, $id)
    {
        if ($request->user()->level === 'PLAYER'){
            return redirect()->route('home');
        }

        if($id == 'new') {
            $module = GameModule::create([
                'gm_id' => $request->user()->id,
                'game' => 'MAGUS',
                'game_module_name' => 'Új MAGUS játék module'
            ]);

            $data = [
                'game_module' => $module,
                'game_data' => []
            ];

            return view('magusgameedit', $data);

        } else {

            $module = GameModule::where('id', $id)->first();
            if (!$module) {
                return redirect()->route('home');
            }

            $gameData = GameModuleData::where('game_module_id', $module->id)->orderBy('game_module_data_order', 'asc')->get();
            $data = [
                'game_module' => $module,
                'game_data' => $gameData
            ];

            return view('magusgameedit', $data);
        }
    }

    /**
     * updating module name
     * 
     * @return json
     */
    public function updateGameModuleName(Request $request, $id){
        
        $moduleName = $request->input('moduleName');
        if (!$moduleName) {
            return response()->json(['error' => 'missing module name data'], 406);
        }
        $gameModule = GameModule::findOrFail($id);
        $gameModule->game_module_name = $moduleName;
        $gameModule->save();
        
       return response()->json('Success', 200);
    }

     /**
     * updating module global notes
     * 
     * @return json
     */
    public function updateGameModuleNote(Request $request, $id){
        
        $moduleNote = $request->input('note');
        if (!$moduleNote) {
            return response()->json(['error' => 'missing module note data'], 406);
        }
        $gameModule = GameModule::findOrFail($id);
        $gameModule->global_note = $moduleNote;
        $gameModule->save();
        
       return response()->json('Success', 200);
    }

    /**
     * get module global notes
     * 
     * @return json
     */
    public function getGameModuleNote(Request $request, $id){
        
        $gameModule = GameModule::findOrFail($id);
        $data = [
            'notes' => $gameModule->global_note
        ];
        
       return response()->json($data, 200);
    }

    /**
     * get module global notes
     * 
     * @return json
     */
    public function getGameModuleNpc(Request $request, $id){
        
        $gameModule = GameModule::findOrFail($id);
        $data = [
            'npcs' => $gameModule->npc_data
        ];
        
       return response()->json($data, 200);
    }

    /**
     * updating module global notes
     * 
     * @return json
     */
    public function updateGameModuleNpc(Request $request, $id){
        
        $moduleNpc = $request->input('npcs');
        if (!$moduleNpc) {
            return response()->json(['error' => 'missing module npc data'], 406);
        }
        $gameModule = GameModule::findOrFail($id);
        $gameModule->npc_data = $moduleNpc;
        $gameModule->save();
        
       return response()->json('Success', 200);
    }

    /**
     * creating new game module data
     * 
     * @return json
     */
    public function createGameModuleData(Request $request, $id){
        
        $moduleOrder = $request->input('moduleOrder', 1);
        $newModuleData = GameModuleData::create([
            'game_module_id' => $id,
            'game_module_data_order' => $moduleOrder,
            'module_data' => [
                'title' => '',
                'description' => '',
                'img' => 'default.jpg',
                'note' => [],
                'npcs' => [],
                'map' => []
            ]
        ]);
        
       return response()->json($newModuleData, 200);
    }

    /**
     * updating game module data
     * 
     * @return json
     */
    public function updateGameModuleData(Request $request, $id){
        
        $moduleData = $request->input('newData');
        if (!$moduleData) {
            return response()->json(['error' => 'missing module data'], 406);
        }
        $gameModule = GameModuleData::findOrFail($id);
        $gameModule->module_data = $moduleData;
        $gameModule->save();
        
       return response()->json($gameModule, 200);
    }
   
    /**
     * updating game module order
     * 
     * @return json
     */
    public function updateGameModuleDataOrder(Request $request, $id){

        $module = GameModule::where('id', $id)->first();
        if (!$module) {
            return redirect()->route('home');
        }
        
        $firsModuleId = $request->input('firstModuleId');
        $firsModuleOrder = $request->input('firstModuleOrder');
        $secondModuleId = $request->input('secondModuleId');
        $secondModuleOrder = $request->input('secondModuleOrder');
        
        $firstGameModule = GameModuleData::findOrFail($firsModuleId);
        $firstGameModule->game_module_data_order = $firsModuleOrder;
        $firstGameModule->save();

        $secondGameModule = GameModuleData::findOrFail($secondModuleId);
        $secondGameModule->game_module_data_order = $secondModuleOrder;
        $secondGameModule->save();

        $gameData = GameModuleData::where('game_module_id', $module->id)->orderBy('game_module_data_order', 'asc')->get();
        
       return response()->json($gameData, 200);
    }

    /**
     * delte game module data
     * 
     * @return json
     */
    public function deleteGameModuleData(Request $request, $id){

        $gameModule = GameModuleData::findOrFail($id);
        $moduleId = $gameModule->game_module_id;
       
        $gameModule->delete();

        $gameData = GameModuleData::where('game_module_id', $moduleId)->orderBy('game_module_data_order', 'asc')->get();
        foreach ($gameData as $index => $data) {
            $data->game_module_data_order = $index + 1;
            $data->save();
        }

        $gameData = GameModuleData::where('game_module_id', $moduleId)->orderBy('game_module_data_order', 'asc')->get();
        
       return response()->json($gameData, 200);
    }
}
