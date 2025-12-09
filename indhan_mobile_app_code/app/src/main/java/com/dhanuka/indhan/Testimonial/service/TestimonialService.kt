package com.dhanuka.indhan.Testimonial.service

import android.app.Notification
import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.Service
import android.content.Context
import android.content.Intent
import android.graphics.BitmapFactory
import android.graphics.Color
import android.graphics.drawable.Icon
import android.os.Build
import android.os.Handler
import android.os.IBinder
import android.os.Looper
import android.widget.Toast
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import com.androidnetworking.AndroidNetworking
import com.androidnetworking.common.Priority
import com.androidnetworking.error.ANError
import com.androidnetworking.interfaces.JSONObjectRequestListener
import com.androidnetworking.interfaces.UploadProgressListener
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Testimonial.Testimonial_MVP.TestimonialFactory
import okhttp3.MediaType
import okhttp3.MultipartBody
import okhttp3.OkHttpClient
import okhttp3.RequestBody
import org.json.JSONObject
import java.io.File
import java.util.concurrent.TimeUnit


class TestimonialService : Service(), TestimonialFactory.TestimonialHomeListener {


    var localBroadcastManager: LocalBroadcastManager? = null
    var notification: Notification? = null

    override fun onBind(intent: Intent?): IBinder? {
        return null
    }

    override fun onCreate() {
        super.onCreate()

        localBroadcastManager = LocalBroadcastManager.getInstance(applicationContext)

    }

    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {

        if (intent != null) {
            var videofile: File? = null
            if (intent.hasExtra(ServiceConstrants.VIDEO_FILE)) {
                videofile = File(intent.getStringExtra(ServiceConstrants.VIDEO_FILE))
            }
            var fileBody1 = RequestBody.create(MediaType.parse("multipart/form-data"), videofile)
            val imagePart1 = MultipartBody.Part.createFormData("video", videofile?.name, fileBody1)

            var desc: String = ""
            if (intent.hasExtra(ServiceConstrants.DESCRIPTION)) {
                desc = intent.getStringExtra(ServiceConstrants.DESCRIPTION) ?: ""
            }
            val description = RequestBody.create(MediaType.parse("multipart/form-data"), desc)

            var testimonial_req = ""
            if (intent.hasExtra(ServiceConstrants.TESTIMONIAL)) {
                testimonial_req = intent.getStringExtra(ServiceConstrants.TESTIMONIAL) ?: ""
            }
            val testi_req =
                RequestBody.create(MediaType.parse("multipart/form-data"), testimonial_req)

            var mob: String = ""
            if (intent.hasExtra(ServiceConstrants.MOBILE)) {
                mob = intent.getStringExtra(ServiceConstrants.MOBILE) ?: ""
            }
            val mobile = RequestBody.create(MediaType.parse("multipart/form-data"), mob)

            var token: String = ""
            if (intent.hasExtra(ServiceConstrants.API_TOKEN)) {
                token = intent.getStringExtra(ServiceConstrants.API_TOKEN) ?: ""
            }
            val token_req = RequestBody.create(MediaType.parse("multipart/form-data"), token)

            var product: String = ""
            if (intent.hasExtra(ServiceConstrants.PRODUCT)) {
                product = intent.getStringExtra(ServiceConstrants.PRODUCT) ?: ""
            }
            val prod = RequestBody.create(MediaType.parse("multipart/form-data"), product)

            var cropName: String = ""
            if (intent.hasExtra(ServiceConstrants.CROP)) {
                cropName = intent.getStringExtra(ServiceConstrants.CROP) ?: ""
            }
            val crop = RequestBody.create(MediaType.parse("multipart/form-data"), cropName)

            var product_id: String = ""
            if (intent.hasExtra(ServiceConstrants.PRODUCT_ID)) {
                product_id = intent.getStringExtra(ServiceConstrants.PRODUCT_ID) ?: ""
            }
            val prodId =
                RequestBody.create(MediaType.parse("multipart/form-data"), product_id.toString())

            var crop_id: String = ""
            if (intent.hasExtra(ServiceConstrants.CROP_ID)) {
                crop_id = intent.getStringExtra(ServiceConstrants.CROP_ID) ?: ""
            }
            val cropId =
                RequestBody.create(MediaType.parse("multipart/form-data"), crop_id.toString())

            var farName: String = ""
            if (intent.hasExtra(ServiceConstrants.FARMER_NAME)) {
                farName = intent.getStringExtra(ServiceConstrants.FARMER_NAME) ?: ""
            }
            val farmerName = RequestBody.create(MediaType.parse("multipart/form-data"), farName)

            val map: HashMap<String, RequestBody> = HashMap()

            map["apiToken"] = token_req
            map["testimonial"] = testi_req
            map["mobile"] = mobile
            map["description"] = description
            map["cropId"] = cropId
            map["productId"] = prodId
            map["farmerName"] = farmerName
            map["crop"] = crop
            map["product"] = prod


            /**
             * create notification
             */
            createNotification()

            Handler(Looper.getMainLooper()).postDelayed({
                AndroidNetworking.initialize(applicationContext)

                val okHttpClient: OkHttpClient = OkHttpClient().newBuilder()
                    .connectTimeout(240, TimeUnit.SECONDS)
                    .readTimeout(240, TimeUnit.SECONDS)
                    .writeTimeout(240, TimeUnit.SECONDS)
                    .build()
                AndroidNetworking.upload("http://dhanukabdapp.in:8088/dhanukaV2/v2/testimonialSubmit")
                    .addMultipartFile("video", videofile)
                    .addMultipartParameter("apiToken", token)
                    .addMultipartParameter("testimonial", testimonial_req)
                    .addMultipartParameter("mobile", mob)
                    .addMultipartParameter("description", desc)
                    .addMultipartParameter("cropId", crop_id)
                    .addMultipartParameter("productId", product_id)
                    .addMultipartParameter("farmerName", farName)
                    .addMultipartParameter("crop", cropName)
                    .addMultipartParameter("product", product)
                    .setTag("uploadTest")
                    .setPriority(Priority.HIGH)
                    .setOkHttpClient(okHttpClient)
                    .build()
                    .setUploadProgressListener(UploadProgressListener { bytesUploaded, totalBytes ->
                        val progress = (bytesUploaded * 100) / totalBytes
                        if (localBroadcastManager != null) {
                            val intent: Intent =
                                Intent(ServiceConstrants.UPLOAD_VIDEO_INTENT_FILETER)
                            intent.putExtra(ServiceConstrants.UPLOADING_PROGRESS, progress)
                            intent.putExtra(
                                ServiceConstrants.UPLOADING_PRODUCT_NAME,
                                videofile!!.name
                            )
                            localBroadcastManager!!.sendBroadcast(intent)
                        }
                    })

                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject?) {

                            if ((response?.get("status")) == "error") {
                                if (localBroadcastManager != null) {
                                    Preference.getInstance(applicationContext)
                                        .putBolean(ServiceConstrants.TESTIMONIAL_UPLOADED, true)
                                    Preference.getInstance(applicationContext)
                                        .putBolean(ServiceConstrants.UPLOADED_SUCCESS, false)
                                    val intent4 =
                                        Intent(ServiceConstrants.UPLOAD_VIDEO_INTENT_FILETER)
                                    intent4.putExtra(ServiceConstrants.UPLOADING_FAILURE, true)
                                    localBroadcastManager!!.sendBroadcast(intent4)
                                }
                            } else {
                                if (localBroadcastManager != null) {
                                    Preference.getInstance(applicationContext)
                                        .putBolean(ServiceConstrants.TESTIMONIAL_UPLOADED, true)
                                    Preference.getInstance(applicationContext)
                                        .putBolean(ServiceConstrants.UPLOADED_SUCCESS, true)
                                    val intent =
                                        Intent(ServiceConstrants.UPLOAD_VIDEO_INTENT_FILETER)
                                    intent.putExtra(ServiceConstrants.UPLOADING_DONE, true)
                                    localBroadcastManager!!.sendBroadcast(intent)
                                }
                            }
                            stopService()
                        }

                        override fun onError(error: ANError) {
                            if (localBroadcastManager != null) {
                                Preference.getInstance(applicationContext)
                                    .putBolean(ServiceConstrants.TESTIMONIAL_UPLOADED, true)
                                Preference.getInstance(applicationContext)
                                    .putBolean(ServiceConstrants.UPLOADED_SUCCESS, false)
                                val intent: Intent =
                                    Intent(ServiceConstrants.UPLOAD_VIDEO_INTENT_FILETER)
                                intent.putExtra(ServiceConstrants.UPLOADING_FAILURE, true)
                                localBroadcastManager!!.sendBroadcast(intent)
                            }
                            stopService()

                        }
                    })
            }, 2000)
        }
        return START_NOT_STICKY
    }

    private fun createNotification() {

        val icon: ByteArray? = null
        val notificationTitle: String = "Uploading"
        val notificationDescription: String = "Video uploading is working..."

        //Notification
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channelId = "001"
            val channelName = "RecordChannel"
            val channel =
                NotificationChannel(channelId, channelName, NotificationManager.IMPORTANCE_NONE)
            channel.lightColor = Color.BLUE
            channel.lockscreenVisibility = Notification.VISIBILITY_PRIVATE
            val manager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
            if (manager != null) {
                manager.createNotificationChannel(channel)

                notification = if (icon != null) {
                    val bmp = BitmapFactory.decodeByteArray(
                        icon,
                        0,
                        icon.size
                    )
                    //Modify notification badge
                    Notification.Builder(applicationContext, channelId)
                        .setOngoing(true)
                        .setSmallIcon(Icon.createWithBitmap(bmp))
                        .setContentTitle(notificationTitle)
                        .setContentText(notificationDescription).build()
                } else {
                    //Modify notification badge
                    Notification.Builder(applicationContext, channelId)
                        .setOngoing(true).setSmallIcon(R.mipmap.ic_launcher)
                        .setContentTitle(notificationTitle)
                        .setContentText(notificationDescription).build()
                }
                startForeground(101, notification)
            }
        } else {
            startForeground(101, Notification())
        }
    }


    private fun stopService() {
        try {
//            val intent = Intent(applicationContext, DataSyncServices::class.java)
//            if (intent != null) {
//                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
//                    startForegroundService(intent)
//                } else {
//                    startService(intent)
//                }
//            }

            try {
                stopForeground(true)
            } catch (e: Exception) {
                e.printStackTrace()
            }

            try {
                stopSelf()
            } catch (e: Exception) {
                e.printStackTrace()
            }

        } catch (e: Exception) {
            e.printStackTrace()
        }
    }

    override fun showProgress() {}

    override fun hideProgress() {}

    override fun onSuccess(model: String) {
        Toast.makeText(applicationContext, model, Toast.LENGTH_SHORT).show()
    }

    override fun onFailure(mes: String) {
        Toast.makeText(applicationContext, mes, Toast.LENGTH_SHORT).show()
    }

}