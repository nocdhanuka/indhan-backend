package com.dhanuka.indhan.DataSyncServices

import android.app.Notification
import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.Service
import android.content.Context
import android.content.Intent
import android.os.Build
import android.os.IBinder
import com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP.SubmitFarmersContracts
import com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP.SubmitFarmersPresenterImpl
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DatabaseHandler
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ImageOfflineModel
import com.dhanuka.indhan.Model.CommonModel
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.SelfParticipationActivity.SP_MVP.SelfParticipationDetailPresenterImpl
import com.dhanuka.indhan.SelfParticipationActivity.SP_MVP.SelfParticipationFactory
import com.dhanuka.indhan.util.AppLogFile
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.LogModel
import com.dhanuka.indhan.util.Utility
import com.google.firebase.analytics.FirebaseAnalytics
import com.google.gson.Gson
import okhttp3.MediaType
import okhttp3.RequestBody
import org.joda.time.DateTime
import java.io.File


class DataSyncServices : Service() {
    override fun onBind(intent: Intent?): IBinder? {
        return null
    }

    lateinit var threadobj: ThreadClass

    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val inportance = NotificationManager.IMPORTANCE_DEFAULT
            val channel = NotificationChannel("indhan", "indhan", inportance)
            val builder = Notification.Builder(this, "indhan")
                .setContentTitle(getString(R.string.app_name))
                .setCategory(Notification.CATEGORY_SERVICE)
                .setContentText("Uploading Pending Data").setChannelId("indhan").setSound(null)
                .setAutoCancel(true)
            val service = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
            service.createNotificationChannel(channel)
            val notification = builder.build()
            startForeground(101, notification)
        }
        threadobj = ThreadClass(this)
        Thread(threadobj).start()
        return super.onStartCommand(intent, flags, startId)
    }

    class ThreadClass(mcontext: Context) : Runnable, SelfParticipationFactory.SPDetailHomeListener,
        SubmitFarmersContracts.ISubmitFarmersView {
        private val TAG = "DataSyncServices"
        var sp_presenter: SelfParticipationFactory.SelfParticipationDetailPresenter
        var submitFarmersPresenter: SubmitFarmersContracts.SubmitFarmersPresenter? = null
        lateinit var model: ImageOfflineModel
        var preference: Preference
        var token: String
        var latitude = "0.00"
        var longitude = "0.00"
        var imgfile: File? = null
        var ImgUrl = ""
        var mcontext: Context
        var db: DatabaseHandler

        init {
            submitFarmersPresenter = SubmitFarmersPresenterImpl(this)
            db = DatabaseHandler(mcontext)
            sp_presenter = SelfParticipationDetailPresenterImpl(this)
            preference = Preference(mcontext)
            token = preference.getString("token")
            latitude = preference.getString(Constants.latitude)
            longitude = preference.getString(Constants.longitude)
            this.mcontext = mcontext
        }

        override fun showProgress() {
        }

        override fun hideProgress() {
        }

        override fun onSuccess(tst: String) {
            try {
                if (model != null) {
                    model.isUploaded = 1
                    DatabaseManager.updateImageOfflineTable(mcontext, model)
                    DatabaseManager.deleteAllImageOfflineTableParticular(
                        mcontext,
                        model.ticket_id.toInt()
                    )
                    startUploading()
                }
            } catch (ex: UninitializedPropertyAccessException) {
                ex.printStackTrace()
            }
        }

        override fun onFailure(mes: String) {
            if (mes.equals("File Already Uploaded!", ignoreCase = true)) {
                DatabaseManager.updateImageOfflineTable(mcontext, model)
                DatabaseManager.deleteAllImageOfflineTableParticular(
                    mcontext,
                    model.ticket_id.toInt()
                )
            } else {
                logEventCustom(mes, false, "Ticket Image Upload")
            }
            startUploading()
        }

        override fun onFailureParticipation(mes: String) {
            logEventCustom(mes, false, "self participation ticket")
            startUploading()
        }

        override fun onFailureFarmers(message: String) {
            logEventCustom(message, false, "farmers error upload")
            startUploading()
        }

        private fun logEventCustom(mes: String, status: Boolean, type: String) {
            val bundle = HashMap<String, Any>()
            bundle["user_detail"] = preference.getString("user_id")
                .plus("_${preference.getString("ecn")}")
                .plus("_${preference.getString("user_type")}")
                .plus(
                    "_${preference.getString("Username")}"
                        .plus("_${preference.getString("designation")}")
                )
            bundle[FirebaseAnalytics.Param.ITEM_ID] = preference.getString("user_id")
            bundle["uploaded_from"] = "data_sync_service"
            bundle["content"] = Gson().toJson(model).toString()
            when {
                type.equals("Ticket Image Upload", ignoreCase = true) -> {
                    bundle[FirebaseAnalytics.Param.CONTENT_TYPE] = "image"
                    bundle["Api_Name"] = "upload_image_ticket"
                    if (status) {
                        bundle["image_success"] = mes
                        bundle[FirebaseAnalytics.Param.ITEM_NAME] = "upload_image_success"
                    } else {
                        bundle["error_upload"] = mes
                        bundle[FirebaseAnalytics.Param.ITEM_NAME] = "upload_image_error"
                    }
                    Utility.logEvent(TAG, "upload_image_ticket", bundle)

                    val log = LogModel()
                    log.apiData = bundle["content"].toString()
                    log.apiName = "Api Name : v2/ticketUpdateImage | $mes"
                    log.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
                    log.userData = bundle["user_detail"].toString()
                    AppLogFile.writeLineInFile(Gson().toJson(log), preference.getString("ecn"))
                }
                type.equals("Farmers Error Upload", ignoreCase = true) -> {
                    bundle[FirebaseAnalytics.Param.CONTENT_TYPE] = "farmerData"
                    bundle["error_upload"] = mes
                    bundle[FirebaseAnalytics.Param.ITEM_NAME] = "upload_farmer_error"
                    bundle["Api_Name"] = "upload_farmers"
                    Utility.logEvent(TAG, "upload_farmer_error", bundle)

                    val log = LogModel()
                    log.apiData = bundle["content"].toString()
                    log.apiName = "Api Name : v2/farmerRegistrationSubmit | $mes"
                    log.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
                    log.userData = bundle["user_detail"].toString()
                    AppLogFile.writeLineInFile(Gson().toJson(log), preference.getString("ecn"))
                }
                else -> {
                    bundle[FirebaseAnalytics.Param.CONTENT_TYPE] = "self participation"
                    bundle["error_upload"] = mes
                    bundle[FirebaseAnalytics.Param.ITEM_NAME] = "upload_self_participation_error"
                    bundle["Api_Name"] = "upload_self_participation"
                    Utility.logEvent(TAG, "upload_self_participation_error", bundle)

                    val log = LogModel()
                    log.apiData = bundle["content"].toString()
                    log.apiName = "Api Name : v2/ticketUpdateImage | v2/farmerRegistrationSubmit | $mes"
                    log.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
                    log.userData = bundle["user_detail"].toString()
                    AppLogFile.writeLineInFile(Gson().toJson(log), preference.getString("ecn"))
                }
            }
        }

        override fun run() {
            if (preference.getBoolean("isLogin")) {
                if (mcontext != null) {
                    startUploading()
                }
            }
        }

        private fun startUploading() {
            if (CheckConnectionStatus.isInternetAvailable(mcontext)) {
                val list = DatabaseManager.getNonUploadedImageOfflineTable(mcontext)
                if (list != null) {
                    if (list.size > 0) {
                        model = list[0]
                        if (model.type.equals(Constants.IS_SELF)) {
                            uploadSelfParticipation()
                        } else {
                            if (model.isTicketOffline == 1) {
                            } else {
                                uploadTicketImage()
                            }
                        }
                    } else {
                        uploadData()
                    }
                } else {
                    uploadData()
                }
            }
        }

        private fun uploadData() {
            val dataToBeSyncedOnServer = db.allContactsThatNeedToSyncedByServer
            if (dataToBeSyncedOnServer != null && dataToBeSyncedOnServer.size > 0) {
                if (CheckConnectionStatus.isInternetAvailable(mcontext)) {
                    val time = preference.getLong("lastFarmerUpdated")
                    if (time < System.currentTimeMillis()) {
                        preference.putLong("lastFarmerUpdated", System.currentTimeMillis() + 600000)
                        submitFarmersPresenter?.onSubmitClickOptimized(
                            dataToBeSyncedOnServer,
                            token
                        )
                    }
                }
            }
        }

        private fun uploadTicketImage() {
            try {
                if (model.isUploaded != 1) {
                    imgfile = File(model.image_url_local)
                    val convertFile2string = imgfile.toString()
                    ImgUrl = imgfile.toString()
                    val fileNAme =
                        convertFile2string.substring(convertFile2string.lastIndexOf("/") + 1)
                    val reqFile = RequestBody.create(MediaType.parse("image/*"), imgfile)
                    val ticket = RequestBody.create(MediaType.parse("text/plain"), model.ticket_id)
                    val tokenReq = RequestBody.create(MediaType.parse("text/plain"), token)
                    val lat = RequestBody.create(
                        MediaType.parse("text/plain"),
                        preference.getString(Constants.latitude)
                    )
                    val long = RequestBody.create(
                        MediaType.parse("text/plain"),
                        preference.getString(Constants.longitude)
                    )
                    val date = RequestBody.create(MediaType.parse("text/plain"), model.date)
                    val map: HashMap<String, RequestBody> = HashMap()
                    map["photo\"; filename=\"$fileNAme\" "] = reqFile
                    map["api_token"] = tokenReq
                    map["tId"] = ticket
                    map["latitude"] = lat
                    map["longitude"] = long
                    map["updated_at"] = date
                    if (CheckConnectionStatus.isInternetAvailable(mcontext)) {
                        sp_presenter.onSubmitPhoto2(map)
                    }
                }
            } catch (ex: UninitializedPropertyAccessException) {
                ex.printStackTrace()
            }

        }

        private fun uploadSelfParticipation() {
            if (model != null && mcontext != null && model.image_url_local.isNotEmpty()) {
                imgfile = File(model.image_url_local)
                val convert_File_2String = imgfile.toString()
                ImgUrl = imgfile.toString()
                val fileNAme =
                    convert_File_2String.substring(convert_File_2String.lastIndexOf("/") + 1)
                val reqFile = RequestBody.create(MediaType.parse("image/*"), imgfile)
                val ticke = RequestBody.create(MediaType.parse("text/plain"), model.ticket_id)
                val description = RequestBody.create(
                    MediaType.parse("text/plain"),
                    if (model.description != null) model.description else ""
                )
                val token_req = RequestBody.create(MediaType.parse("text/plain"), token)
                val lat = RequestBody.create(
                    MediaType.parse("text/plain"),
                    if (model.lattitude != null) model.lattitude else "0.00"
                )
                val long = RequestBody.create(
                    MediaType.parse("text/plain"),
                    if (model.longitude != null) model.longitude else "0.00"
                )
                val map: HashMap<String, RequestBody> = HashMap()
                map.put("photo\"; filename=\"$fileNAme\" ", reqFile)
                map.put("apiToken", token_req)
                map.put("tId", ticke)
                map.put("desc", description)
                map.put("latitude", lat)
                map.put("longitude", long)
                if (CheckConnectionStatus.isInternetAvailable(mcontext)) {
                    sp_presenter.onSubmitPhoto(map)
                }
            }
        }

        override fun setItems(items: CommonModel) {
            if (items.status_code == 200) run {
                db.updateSyncStatus()
            }
        }
    }
}

