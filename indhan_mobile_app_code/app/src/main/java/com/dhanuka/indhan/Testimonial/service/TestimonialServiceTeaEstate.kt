package com.dhanuka.indhan.Testimonial.service

import android.app.Notification
import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.Service
import android.content.Context
import android.content.Intent
import android.graphics.Color
import android.os.Build
import android.os.Handler
import android.os.IBinder
import android.os.Looper
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import com.androidnetworking.AndroidNetworking
import com.androidnetworking.common.Priority
import com.androidnetworking.error.ANError
import com.androidnetworking.interfaces.JSONObjectRequestListener
import com.dhanuka.indhan.DataSyncServices.DataSyncServices
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.RetrofitClient.LIVE_URL
import okhttp3.OkHttpClient
import org.json.JSONObject
import java.io.File
import java.util.concurrent.TimeUnit


class TestimonialServiceTeaEstate : Service() {
    var localBroadcastManager: LocalBroadcastManager? = null
    private var notification: Notification? = null

    override fun onBind(intent: Intent?): IBinder? {
        return null
    }

    override fun onCreate() {
        super.onCreate()
        localBroadcastManager = LocalBroadcastManager.getInstance(applicationContext)
    }

    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
        intent?.let {
            val imageVideo = it.getStringExtra("image_video")?.let { it2 -> File(it2) }
            val description = it.getStringExtra("description") ?: ""
            val teaId = it.getStringExtra("tea_estate_id") ?: ""
            val teaContactId = it.getStringExtra("tea_contact_id") ?: ""
            val contactName = it.getStringExtra("contact_name") ?: ""
            val languageId = it.getStringExtra("language_id") ?: ""
            val languageName = it.getStringExtra("language_name") ?: ""
            val contactDesignation = it.getStringExtra("contact_designation") ?: ""
            val contactNumber = it.getStringExtra("contact_number") ?: ""
            val waContactNumber = it.getStringExtra("wa_contact_number") ?: ""
            val canBeShared = it.getStringExtra("can_be_shared") ?: ""
            val latitude = it.getStringExtra("latitude") ?: ""
            val longitude = it.getStringExtra("longitude") ?: ""
            val dateTime = it.getStringExtra("date_time") ?: ""
            val institutionalType = it.getStringExtra("institutional_type") ?: ""
            val testimonialName = it.getStringExtra("testimonial_name") ?: ""
            val institutionalName = it.getStringExtra("institutional_name") ?: ""
            val userEcn = it.getStringExtra("user_ecn") ?: ""
            val officeType = it.getStringExtra("office_type") ?: ""
            val type = it.getStringExtra("type") ?: ""
            createNotification()
            Handler(Looper.getMainLooper()).postDelayed({
                AndroidNetworking.initialize(applicationContext)

                val okHttpClient: OkHttpClient = OkHttpClient().newBuilder()
                    .connectTimeout(240, TimeUnit.SECONDS)
                    .readTimeout(240, TimeUnit.SECONDS)
                    .writeTimeout(240, TimeUnit.SECONDS)
                    .build()
                AndroidNetworking.upload(LIVE_URL + "v3/createInstitutionalTestimonial")
                    .addMultipartFile("image_video", imageVideo)
                    .addMultipartParameter("description", description)
                    .addMultipartParameter("institutional_id", teaId)
                    .addMultipartParameter("language_id", languageId)
                    .addMultipartParameter("language_name", languageName)
                    .addMultipartParameter("institutional_id", teaId)
                    .addMultipartParameter("institutional_contact_id", teaContactId)
                    .addMultipartParameter("institutionalType", institutionalType)
                    .addMultipartParameter("contact_name", contactName)
                    .addMultipartParameter("institutional_name", institutionalName)
                    .addMultipartParameter("user_ecn", userEcn)
                    .addMultipartParameter("office_type", officeType)
                    .addMultipartParameter("type", type)
                    .addMultipartParameter("contact_designation", contactDesignation)
                    .addMultipartParameter("contact_number", contactNumber)
                    .addMultipartParameter("wa_contact_number", waContactNumber)
                    .addMultipartParameter("can_be_shared", canBeShared)
                    .addMultipartParameter("latitude", latitude)
                    .addMultipartParameter("testimonial_name", testimonialName)
                    .addMultipartParameter("longitude", longitude)
                    .addMultipartParameter("date_time", dateTime)
                    .setTag("uploadTestimonial2")
                    .setPriority(Priority.HIGH)
                    .setOkHttpClient(okHttpClient)
                    .build()
                    .setUploadProgressListener { bytesUploaded, totalBytes ->
                        val progress = (bytesUploaded * 100) / totalBytes
                        if (localBroadcastManager != null) {
                            val intent1 =
                                Intent(ServiceConstrants.UPLOAD_VIDEO_INTENT_FILETER)
                            intent1.putExtra(ServiceConstrants.UPLOADING_PROGRESS, progress)
                            intent1.putExtra(
                                ServiceConstrants.UPLOADING_PRODUCT_NAME,
                                imageVideo?.name
                            )
                            localBroadcastManager!!.sendBroadcast(intent1)
                        }
                    }

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
                                    val intent3 =
                                        Intent(ServiceConstrants.UPLOAD_VIDEO_INTENT_FILETER)
                                    intent3.putExtra(ServiceConstrants.UPLOADING_DONE, true)
                                    localBroadcastManager!!.sendBroadcast(intent3)
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
                                val intent2 =
                                    Intent(ServiceConstrants.UPLOAD_VIDEO_INTENT_FILETER)
                                intent2.putExtra(ServiceConstrants.UPLOADING_FAILURE, true)
                                localBroadcastManager!!.sendBroadcast(intent2)
                            }
                            stopService()

                        }
                    })
            }, 2000)
        }
        return START_NOT_STICKY
    }

    private fun createNotification() {

        val notificationTitle = "Uploading"
        val notificationDescription = "Video uploading is working..."

        //Notification
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channelId = "19868"
            val channelName = "RecordChannel2"
            val channel =
                NotificationChannel(channelId, channelName, NotificationManager.IMPORTANCE_NONE)
            channel.lightColor = Color.BLUE
            channel.lockscreenVisibility = Notification.VISIBILITY_PRIVATE
            val manager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
            manager.createNotificationChannel(channel)
            notification = Notification.Builder(applicationContext, channelId)
                .setOngoing(true).setSmallIcon(R.drawable.app_icon)
                .setContentTitle(notificationTitle)
                .setContentText(notificationDescription).build()
            startForeground(1001, notification)
        } else {
            startForeground(1001, Notification())
        }
    }

    private fun stopService() {
        try {
            val intent = Intent(applicationContext, DataSyncServices::class.java)
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                startForegroundService(intent)
            } else {
                startService(intent)
            }

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
}