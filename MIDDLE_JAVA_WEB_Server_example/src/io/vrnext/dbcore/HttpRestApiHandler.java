/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package io.vrnext.dbcore;

import static io.netty.handler.codec.http.HttpResponseStatus.BAD_REQUEST;
import static io.netty.handler.codec.http.HttpResponseStatus.OK;
import static io.netty.handler.codec.http.HttpVersion.HTTP_1_1;
import io.netty.buffer.Unpooled;
import io.netty.channel.ChannelFutureListener;
import io.netty.channel.ChannelHandlerContext;
import io.netty.channel.SimpleChannelInboundHandler;
import io.netty.handler.codec.http.DefaultFullHttpResponse;
import io.netty.handler.codec.http.FullHttpResponse;
import io.netty.handler.codec.http.HttpHeaderNames;
import io.netty.handler.codec.http.HttpHeaderValues;
import io.netty.handler.codec.http.HttpObject;
import io.netty.handler.codec.http.HttpRequest;
import io.netty.handler.codec.http.HttpUtil;
import io.netty.handler.codec.http.LastHttpContent;
import io.netty.util.CharsetUtil;
import io.netty.handler.codec.http.QueryStringDecoder;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import java.util.HashMap;
import io.vrnext.dbcore.Handlers.RequestHandlerFactory;
import io.vrnext.dbcore.Request.RequestArgument;
import io.vrnext.dbcore.Handlers.IRequestHandler;
import com.google.gson.Gson;
import io.vrnext.dbcore.Handlers.CataloguesMaterialsFull;
import io.vrnext.dbcore.Handlers.RequestHandlerByOneTable;
import io.vrnext.dbcore.Handlers.RequestHandlerEmpty;
import io.vrnext.dbcore.Handlers.ProductSearch;
import io.vrnext.dbcore.Handlers.PlayerConfig;
import io.vrnext.dbcore.Handlers.CompaniesStoreByProductUniq;
import org.postgresql.util.GettableHashMap;

/**
 *
 * @author Leonid
 */
public class HttpRestApiHandler extends SimpleChannelInboundHandler<Object> {

    private static final Logger logger = LogManager.getLogger(HttpRestApiHandler.class.getName());
    private HashMap<String, Class> routing;
    private HttpRequest request;
    private int count;

    StringBuilder responseData = new StringBuilder();

    public HttpRestApiHandler() {
        routing = new GettableHashMap<>();
        routing.put("CataloguesMaterialsFull", CataloguesMaterialsFull.class);
        routing.put("RequestHandlerByOneTable", RequestHandlerByOneTable.class);
        routing.put("RequestHandlerEmpty", RequestHandlerEmpty.class);
        routing.put("ProductSearch", ProductSearch.class);   
        routing.put("PlayerConfig", PlayerConfig.class); 
        routing.put("CompaniesStoreByProductUniq", CompaniesStoreByProductUniq.class); 
    }
    
    @Override
    public void channelReadComplete(ChannelHandlerContext ctx) {
        ctx.flush();
    }

    @Override
    protected void channelRead0(ChannelHandlerContext ctx, Object msg)
        throws ClassNotFoundException, InstantiationException, IllegalAccessException, NoSuchMethodException {

        long millis_startTime = System.currentTimeMillis();

        HashMap modelResult;
        String responseType   = "json";
        String response       = "[]";
        Object parametersPost = null;
        String[] tableName    = new String[1];
        tableName[0]          = "";
        
        String modelName = "RequestHandlerEmpty";
        Class className = RequestHandlerEmpty.class;

        Gson gson = new Gson();
        RequestHandlerFactory modelFactory = new RequestHandlerFactory();

        if (msg instanceof HttpRequest) {
            request = (HttpRequest) msg;
            responseData.setLength(0);
        }

        String methodName = request.method().name();
        String[] segments = request.uri().split("/");
        
        if (segments.length > 1) {
            responseType = segments[1];
        }

        if (segments.length > 2) {  
            modelName        = "";
            int indexDicrise = 0;            
            int indexQuery   = segments[segments.length - 1].indexOf("?");

            if (indexQuery == 0) {
                indexDicrise = 1;
            }

            for (int i = 2; i < segments.length - indexDicrise; i++) {
                modelName = modelName + segments[i].substring(0, 1).toUpperCase() + segments[i].substring(1);
            }
        } 
 
        if ((segments.length == 3 || segments.length == 4) 
                && !modelName.equals("Product_search")
                && !modelName.equals("Companies_store_by_product_uniq")
                && !modelName.equals("Player_config")
        ) {
             modelName = "RequestHandlerByOneTable";
             tableName[0] = "_" + segments[2];
        } 
        
        if (modelName.equals("Product_search")) {
             modelName = "ProductSearch";          
        } 
        
        if (modelName.equals("Companies_store_by_product_uniq")) {
             modelName = "CompaniesStoreByProductUniq";          
        } 
        
        if (modelName.equals("Player_config")) {
             modelName = "PlayerConfig";          
        } 

        if(routing.containsKey(modelName)) {
            className = routing.get(modelName);
        }

        IRequestHandler model = (IRequestHandler) modelFactory.getModel(className.getName());

        switch (methodName) {
            case ("GET"):
                count = 0;
                QueryStringDecoder decoder = new QueryStringDecoder(request.uri());
                RequestArgument[] parametersGet = new RequestArgument[decoder.parameters().size() + 1];
                decoder.parameters().forEach((k, valueList) -> {
                    String[] strarray = new String[valueList.size()];
                    parametersGet[count] = new RequestArgument(k, valueList.toArray(strarray));
                    count++;
                });
                parametersGet[count] = new RequestArgument("tableName", tableName);                
                modelResult = model.GET(gson.toJson(parametersGet));
                break;
            case ("POST"):
                if (parametersPost != null) {
                    parametersPost = parametersPost.toString();
                }
                modelResult = model.POST(gson.toJson(parametersPost));
                break;
            case ("PUT"):
                Object parametersPut = null;
                modelResult = model.PUT(gson.toJson(parametersPut));
                break;
            case ("DELETE"):
                Object parametersDel = null;
                modelResult = model.DELETE(gson.toJson(parametersDel));
                break;
            default:
                modelResult = model.GET(null);
                break;
        }

        switch (responseType) {
            case ("json"):
                response = gson.toJson(modelResult);
                break;
            case ("object"):
                response = gson.toJson(modelResult);
                break;
            default:
                response = gson.toJson(modelResult);
                break;
        }

        if (msg instanceof LastHttpContent) {
            LastHttpContent trailer = (LastHttpContent) msg;
            writeResponse(ctx, trailer, responseData);
        }

        responseData.append(response);

        long millis_endTime = System.currentTimeMillis();
     //   logger.info("Time taken in milli seconds: " + (millis_endTime - millis_startTime));

    }

    @Override
    public void exceptionCaught(ChannelHandlerContext ctx, Throwable cause) {
        cause.printStackTrace();
        ctx.close();
    }


    private void writeResponse(ChannelHandlerContext ctx, LastHttpContent trailer, StringBuilder responseData) {
        boolean keepAlive = HttpUtil.isKeepAlive(request);

        FullHttpResponse httpResponse = new DefaultFullHttpResponse(HTTP_1_1, ((HttpObject) trailer).decoderResult()
                .isSuccess() ? OK : BAD_REQUEST, Unpooled.copiedBuffer(responseData.toString(), CharsetUtil.UTF_8));

        httpResponse.headers()
                .set(HttpHeaderNames.CONTENT_TYPE, "text/plain; charset=UTF-8");

        if (keepAlive) {
            httpResponse.headers()
                    .setInt(HttpHeaderNames.CONTENT_LENGTH, httpResponse.content()
                            .readableBytes());
            httpResponse.headers()
                    .set(HttpHeaderNames.CONNECTION, HttpHeaderValues.KEEP_ALIVE);
        }

        ctx.write(httpResponse);

        if (!keepAlive) {
            ctx.writeAndFlush(Unpooled.EMPTY_BUFFER)
                    .addListener(ChannelFutureListener.CLOSE);
        }
    }
}
