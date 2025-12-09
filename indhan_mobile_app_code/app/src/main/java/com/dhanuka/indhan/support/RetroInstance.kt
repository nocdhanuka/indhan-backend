package com.dhanuka.indhan.support

import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

class RetroInstance {
    companion object {
        fun getRetrofitInstance(): Retrofit {
            return Retrofit.Builder()
//                    .baseUrl("https://udf.vga.mybluehostin.me/dhanuka/")
                .baseUrl("http://dhanukabdapp.in:8088/dhanukaV2/")
                .addConverterFactory(GsonConverterFactory.create())
                .build()
        }
    }
}