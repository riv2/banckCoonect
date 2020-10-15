package io.vrnext.dbcore.Handlers;
import com.google.gson.Gson;
import io.vrnext.dbcore.Request.RequestArgument;
import java.util.HashMap;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.db.JDBCMapper;

/**
 *
 * @author korni
 */
public class PlayerConfig implements IRequestHandler { 
    
    private static final Logger logger = LogManager.getLogger(PlayerConfig.class.getName());

    @Override
    public HashMap GET(String args) {      
        
        String member_uniq     = "";
        String catalogue_mode  = "";
        String JsonConfig      = "";
  
        Gson gson = new Gson();
        JDBCMapper maper = new JDBCMapper();
        HashMap<String, Object> responceEntities = new HashMap();
        HashMap<String, String> _player_config = new HashMap();        
        
        RequestArgument[] params = gson.fromJson(args, RequestArgument[].class);

        // Requests
        for (int i = 0; i < params.length; i++) {
            if (params[i].key.toUpperCase().equals("MEMBER_UNIQ")) {
                member_uniq = params[i].value[0];
            }  
            if (params[i].key.toUpperCase().equals("CATALOGUE_MODE")) {
                catalogue_mode = params[i].value[0];
            }          
        }
        
        //todo 
        _player_config.put("button_help", "main_control_child_block_left");

        if (catalogue_mode.equals("")) {
           _player_config.put("button_customize", "main_control_child_block_left"); 
        } else {
           _player_config.put("button_catalogue", "main_control_child_block_left"); 
        }  

        _player_config.put("full_screen", "main_control_child_block_right");
        _player_config.put("screen_shot", "main_control_child_block_right");
        _player_config.put("button_calc", "main_control_child_block_right");

        responceEntities.put("_player_config", _player_config); 
        
        return responceEntities;
    }    
    
    @Override
    public HashMap PUT(String parametrs) {
        HashMap<String, Object> responceEntities = new HashMap();
        return responceEntities;
    }

    @Override
    public HashMap POST(String parametrs) {
        HashMap<String, Object> responceEntities = new HashMap();
        return responceEntities;
    }

    @Override
    public HashMap DELETE(String parametrs) {
        HashMap<String, Object> responceEntities = new HashMap();
        return responceEntities;
    }     
    
}
