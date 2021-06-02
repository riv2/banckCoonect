/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package io.vrnext.dbcore;

import io.netty.bootstrap.ServerBootstrap;
import io.netty.channel.ChannelFuture;
import io.netty.channel.ChannelInitializer;
import io.netty.channel.ChannelPipeline;
import io.netty.channel.EventLoopGroup;
import io.netty.channel.nio.NioEventLoopGroup;
import io.netty.channel.socket.SocketChannel;
import io.netty.channel.socket.nio.NioServerSocketChannel;
import io.netty.handler.codec.http.HttpRequestDecoder;
import io.netty.handler.codec.http.HttpResponseEncoder;
import io.netty.handler.codec.http.HttpObjectAggregator;

/**
 *
 * @author Leonid
 */
public class Main {

    private int port;

    public Main(int port) {
        this.port = port;
    }

    public void run() throws Exception {
        EventLoopGroup bossGroup = new NioEventLoopGroup(1);
        EventLoopGroup workerGroup = new NioEventLoopGroup();
        try {
            ServerBootstrap b = new ServerBootstrap();
            b.group(bossGroup, workerGroup)
                .channel(NioServerSocketChannel.class)
                .childHandler(new ChannelInitializer<SocketChannel>() {
                    @Override
                    protected void initChannel(SocketChannel ch) throws Exception {                       
                        ChannelPipeline p = ch.pipeline();
	                p.addLast(new HttpRequestDecoder(163840,8192,8192));
                        p.addLast(new HttpResponseEncoder());
                        p.addLast(new HttpRestApiHandler());                        
                        p.addLast(new HttpObjectAggregator(1048576));
                    }
                });

            ChannelFuture f = b.bind(port)
                .sync();
            
            f.channel()
                .closeFuture()
                .sync();

        } finally {
            bossGroup.shutdownGracefully();
            workerGroup.shutdownGracefully();
        }
    }


    public static void main(String[] args) throws Exception {
        int port = 8000;
        if (args.length > 0) {
            port = Integer.parseInt(args[0]);
        }

        new Main(port).run();
    }
}
