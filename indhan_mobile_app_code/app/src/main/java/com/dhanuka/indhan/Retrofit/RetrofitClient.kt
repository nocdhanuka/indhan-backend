package com.dhanuka.indhan.Retrofit

import android.text.TextUtils
import com.dhanuka.indhan.Preference.Preference
import okhttp3.Interceptor
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.adapter.rxjava.RxJavaCallAdapterFactory
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit

object RetrofitClient {
        const val LIVE_URL = "http://dhanukabdapp.in:8088/dhanukaV2/" // LIVE
//    const val LIVE_URL = "https://udf.vga.mybluehostin.me/dhanuka/" // LIVE

    val logging = HttpLoggingInterceptor().setLevel(HttpLoggingInterceptor.Level.BODY)

    var httpClient =
        OkHttpClient.Builder() //here we can add Interceptor for dynamical adding headers
            .addInterceptor(Interceptor { chain ->
                val request = chain.request().newBuilder()
                if (!TextUtils.isEmpty(Preference.getInstance().getString("token"))) {
                    request.addHeader("Authorization", Preference.getInstance().getString("token"))
                        .build()
                }
                return@Interceptor chain.proceed(request.build())
            }).addInterceptor(logging) //here // we adding Interceptor for full level logging
            .readTimeout(600, TimeUnit.SECONDS)
            .connectTimeout(600, TimeUnit.SECONDS)
            .build()

    private var retrofit: Retrofit? = null
    val mvpClient: Retrofit
        get() {
            if (retrofit == null) {
                retrofit = Retrofit.Builder()
                    .baseUrl(LIVE_URL)
                    .addConverterFactory(GsonConverterFactory.create())
                    .addCallAdapterFactory(RxJavaCallAdapterFactory.create())
                    .client(httpClient)
                    .build()
            }
            return retrofit!!
        }
}