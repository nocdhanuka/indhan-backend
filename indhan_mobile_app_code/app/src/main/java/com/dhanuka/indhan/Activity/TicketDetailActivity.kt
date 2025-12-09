package com.dhanuka.indhan.Activity

import android.Manifest
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.graphics.BitmapFactory
import android.location.Location
import android.location.LocationManager
import android.os.Bundle
import android.text.TextUtils
import android.util.Log
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.core.app.ActivityCompat
import com.bumptech.glide.Glide
import com.dhanuka.indhan.Activity.EditTicketMVP.EditTicketContracts
import com.dhanuka.indhan.Activity.EditTicketMVP.EditTicketPresenterImpl
import com.dhanuka.indhan.Activity.EditTicketMVP.Model.EditTicketBean
import com.dhanuka.indhan.Activity.TicketDetailImageOffline.TicketDetailsOfflineActivity
import com.dhanuka.indhan.Activity.TicketDetailMVP.EndTicketMVP.EndTicketContracts
import com.dhanuka.indhan.Activity.TicketDetailMVP.EndTicketMVP.EndTicketPresenterImpl
import com.dhanuka.indhan.Activity.TicketDetailMVP.EndTicketMVP.Model.EndTicketBean
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DatabaseHandler
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ImageOfflineModel
import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.*
import com.dhanuka.indhan.util.AppLogFile.writeLineInFile
import com.dhanuka.indhan.util.Utility.logEvent
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import com.google.firebase.analytics.FirebaseAnalytics
import com.google.gson.Gson
import com.himangi.imagepreview.ImagePreviewActivity
import io.nlopez.smartlocation.SmartLocation
import kotlinx.android.synthetic.main.activity_ticket_detail.*
import okhttp3.MediaType
import okhttp3.RequestBody
import org.joda.time.DateTime
import pl.aprilapps.easyphotopicker.DefaultCallback
import pl.aprilapps.easyphotopicker.EasyImage
import pl.aprilapps.easyphotopicker.MediaFile
import pl.aprilapps.easyphotopicker.MediaSource
import rx.Single
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.io.File
import java.text.SimpleDateFormat
import java.util.*
import java.util.concurrent.TimeUnit
import kotlin.math.roundToInt

/**
 * Created by Shivam on 3/6/2018.
 */

class TicketDetailActivity : BaseActivity(), EditTicketContracts.IEditTicketView,
    EndTicketContracts.IEndTicketView {

    override fun setVillageList(items: MyVillageBean) {}
    private var easyImage: EasyImage? = null
    private lateinit var preference: Preference
    var userType = ""
    var isImageUploaded = ""
    private lateinit var mNewTicketPresenter: EditTicketContracts.EditTicketPresenter
    private lateinit var endTicketPresenter: EndTicketContracts.EndTicketPresenter
    private var Token = ""
    private lateinit var ticket: TicketHistoryModel.Datum
    private lateinit var db: DatabaseHandler
    var ImgUrl = ""
    var dbUpdateModel = ImageOfflineModel()
    var isActiveLoader = false
    val times: List<Long> = listOf(
        TimeUnit.DAYS.toMillis(365),
        TimeUnit.DAYS.toMillis(30),
        TimeUnit.DAYS.toMillis(1),
        TimeUnit.HOURS.toMillis(1),
        TimeUnit.MINUTES.toMillis(1),
        TimeUnit.SECONDS.toMillis(1)
    )
    val timesString = listOf("year", "month", "day", "hour", "minute", "second")

    private var latitude: Double? = null
    private var longtitude: Double? = null
    private var userLocation: Location? = null
    private lateinit var fusedLocationClient: FusedLocationProviderClient
    private var mLocationManager: LocationManager? = null
    var mLastLocation: Location? = null

    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@TicketDetailActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_ticket_detail)
        db = DatabaseHandler(this)
        initCamera()
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this)
        getLocation()
        //dd name visible in case of BDO only
        preference = Preference(this)
        userType = preference.getString("user_type")

        if (userType.equals("BDO", false)) {
            llDd.visibility = View.VISIBLE
        } else {
            llDd.visibility = View.GONE
        }
        //end

        val extras = intent.extras
        //ticketId = extras.getInt("ticketId")
        ticket = extras!!.getSerializable("ticket") as TicketHistoryModel.Datum
        mNewTicketPresenter = EditTicketPresenterImpl(this)

        Token = Preference.getInstance(this).getString("token")
        tvReTicketNo.setText(ticket.ticket)
        setItemsTicket(ticket)
        //ticketDetailPresenter = TicketDetailPresenterImpl(this)
        endTicketPresenter = EndTicketPresenterImpl(this)

        ivBackTicket.setOnClickListener(this)
        ivEditTicket.setOnClickListener(this)
        btnRbEndTicket.setOnClickListener(this)
        img.setOnClickListener(this)
        img_src.setOnClickListener(this)
        layout_btn.setOnClickListener(this)

        tvReLanguage.text = "- -"
        if (ticket.language != null) {
            tvReLanguage.text = ticket.language
        }


        if (ticket.ticketStatus == "ACTIVE") {
            ivEditTicket.visibility = View.VISIBLE
            btnRbEndTicket.visibility = View.VISIBLE
            img.visibility = View.VISIBLE
            if (ticket.dataSource.equals("IFC", ignoreCase = true)) {
                img.visibility = View.GONE
                layout_btn.visibility = View.GONE
            } else {
                img.visibility = View.VISIBLE
                layout_btn.visibility = View.VISIBLE
            }
        } else if (ticket.ticketStatus.equals("INACTIVE")) {
            ivEditTicket.visibility = View.GONE
            btnRbEndTicket.visibility = View.GONE
            img.visibility = View.GONE
            if (ticket.dataSource.toString().equals("IFC", ignoreCase = true)) {
                layout_btn.visibility = View.GONE
            }
        }
    }

    private fun openPicture() {
        ImagePickerUtil.pickFromCamera2(easyImage, this@TicketDetailActivity, { imageFile, _ ->
            var file = ImageUtils.compressToExistingFile(
                imageFile.absolutePath,
                imageFile
            )
            if(file == null){
                file = imageFile
            }
            val bitmap = BitmapFactory.decodeFile(file.path.toString())
            if (bitmap != null) {
                img_src.visibility = View.VISIBLE
                img_src.setImageBitmap(bitmap)
                dbUpdateModel.image_url_local = file.path.toString()
                ImgUrl = file.path.toString()
                dbUpdateModel.ticket_id = ticket.tID.toString()
                dbUpdateModel.isUploaded = 0
                dbUpdateModel.type = Constants.IS_TICKET
                val cal = Calendar.getInstance(Locale.ENGLISH)
                val format1 = SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
                val formatted = format1.format(cal.time)
                dbUpdateModel.date = formatted
                functionFetchLocation(file)
                if (ticket.isOffline)
                    updateImageUploadedInDB()
            }
        }, "")
    }

    private fun showImageInFullScreen() {
        val intent = Intent(this, ImagePreviewActivity::class.java)
        if (ImgUrl.length > 5) {
            val imageList: ArrayList<String> = ArrayList()
            imageList.add(ImgUrl)
            intent.putExtra(ImagePreviewActivity.IMAGE_LIST, imageList)
            intent.putExtra(ImagePreviewActivity.CURRENT_ITEM, 0)
            startActivity(intent)
        }
    }

    override fun onClick(v: View) {
        when (v.id) {

            R.id.ivBackTicket -> {
                val intent = Intent()
                setResult(RESULT_OK, intent)
                onBackPressed()
            }

            R.id.img -> {
                if (!userType.equals("dhanuka_doctor", false)) {
                    if (ticket.uploadedByType != null) {
                        if (ticket.uploadedByType == "" && isImageUploaded == "") {
                            if (latitude == null || longtitude == null) {
                                getLocation()
                            } else {
                                openPicture()
                            }
                        } else {
                            makeToast("Image has been already uploaded by ${ticket.uploadedByType}")
                        }
                    }
                } else {
                    if (ticket.ddTicketRole == "1") {
                        if (ticket.uploadedByType == "" && isImageUploaded == "") {
                            if (latitude == null || longtitude == null) {
                                getLocation()
                            } else {
                                openPicture()
                            }
                        } else {
                            makeToast("Image has been already uploaded by ${ticket.uploadedByType}")
                        }
                    } else {
                        makeToast("Only Fl can upload image")
                    }
                }
            }

            R.id.img_src -> {
                showImageInFullScreen()
            }

            R.id.btnRbEndTicket -> {
                if (ImgUrl.isEmpty() && !ticket.dataSource.equals("IFC", ignoreCase = true)) {
                    imageAddDialogAlert("Are you sure you want to close this ticket without adding image?")
                } else {
                    endTicketAlert()
                }
            }

            R.id.ivEditTicket -> {
                val extras = Bundle()
                if (ticket.tID == null) ticket.id else ticket.tID?.let {
                    extras.putInt("ticketId", it)
                }
                extras.putInt("rowId", ticket.id)
                extras.putString("ticketNumber", ticket.ticket)
                val intent = Intent(this, EditTicketActivity::class.java)
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
                intent.putExtras(extras)
                startActivityForResult(intent, 1)
            }

            R.id.layout_btn -> {
                val intent = Intent(this, TicketDetailsOfflineActivity::class.java)
                intent.putExtra("tid", ticket.tID)
                startActivity(intent)
            }
        }

    }

    override fun hideProgress() {
        dismissProgress()
    }

    private fun checkOfflineUpload(tid: String) {
        val imageModel = DatabaseManager.getNonUploadedImageOfflineTable(this, tid)
        if (imageModel != null) {
            val imgfile = File(imageModel.image_url_local)
            val bitmap = BitmapFactory.decodeFile(imgfile.toString())
            if (bitmap != null) {
                img_src.visibility = View.VISIBLE
                img_src.setImageBitmap(bitmap)
            } else {
                img_src.visibility = View.GONE
            }
        } else {
            img_src.visibility = View.GONE
        }
    }

    private fun functionFetchLocation(path: File) {
        if (CheckConnectionStatus.isInternetAvailable(this@TicketDetailActivity)) {
            val imgfile = File(path.toString())
            val convert_File_2String = imgfile.toString()
            ImgUrl = imgfile.toString()
            val fileNAme = convert_File_2String.substring(convert_File_2String.lastIndexOf("/") + 1)
            val reqFile = RequestBody.create(MediaType.parse("image/*"), imgfile)
            val ticke = RequestBody.create(MediaType.parse("text/plain"), ticket.tID.toString())
            val token_req = RequestBody.create(MediaType.parse("text/plain"), Token)
            val lat = RequestBody.create(MediaType.parse("text/plain"), latitude.toString())
            val long = RequestBody.create(MediaType.parse("text/plain"), longtitude.toString())
            val date = RequestBody.create(MediaType.parse("text/plain"), dbUpdateModel.date)
            mNewTicketPresenter.onSubmitPhoto(reqFile, ticke, token_req, fileNAme, lat, long, date)
            logEventTicketSelfParticipation("", false)
        } else {
            dbUpdateModel.lattitude = latitude.toString()
            dbUpdateModel.longitude = longtitude.toString()
            DatabaseManager.saveImageOfflineTable(
                this@TicketDetailActivity,
                dbUpdateModel,
                if (ticket.isOffline) 1 else 0
            )
            logEventTicketSelfParticipation("Offline Data", false)
        }
    }

    private fun setItemsTicket(items: TicketHistoryModel.Datum) {
        //  if (items.status_code == 200) {
        tvReDataSource.setText(items.dataSource)
        if (items.uploadedFile != "") {
            ImgUrl = items.uploadedFile
            txtUploadBy.visibility = View.VISIBLE
            txtUploadBy.text = "Uploaded By ${items.uploadedByType}"
        }

        if (!TextUtils.isEmpty(items.activityDuration)) {
            if (items.activityDuration.toString().toDouble().toInt() > 0) {
                llDuration.visibility = View.VISIBLE
                var duration: String = ""
                duration = if (items.activityDuration.contains(".")) {
                    "- ${items.activityDuration.split(".")[0].toInt()} hours " +
                            "0.${items.activityDuration.split(".")[1]}".toDouble().times(60)
                                .toInt() + " minutes"
                } else {
                    "- ${(items.activityDuration.toDouble().roundToInt())} hours"
                }
                tvDuration.text = duration
            } else {
                llDuration.visibility = View.GONE
            }
        } else {
            llDuration.visibility = View.GONE
        }

        if (!TextUtils.isEmpty(items.totalPDA)) {
            llTotalPDA.visibility = View.VISIBLE
            val pda: String = "- ".plus(items.totalPDA)
            tvPDA.text = pda
        } else {
            llTotalPDA.visibility = View.GONE
        }

        if (!TextUtils.isEmpty(items.total_activity_distance)) {
            if (items.total_activity_distance != "0") {
                llTotalDistance.visibility = View.VISIBLE
                val distance: Double = items.total_activity_distance.toDouble() / 1000
                val distanceString = "- ${Utility.roundOffDecimal(distance)} Km"
                tvTotalDistance.text = distanceString
            } else {
                llTotalDistance.visibility = View.GONE
            }
        } else {
            llTotalDistance.visibility = View.GONE
        }

        if (!TextUtils.isEmpty(items.activityDuration) || !TextUtils.isEmpty(items.totalPDA) || !TextUtils.isEmpty(
                items.total_activity_distance
            )
        ) {
            llDurationPDA.visibility = View.VISIBLE
        } else {
            llDurationPDA.visibility = View.GONE
        }
        if (items.dataSource.equals("Field Day")) {
            txtDemoRequestHeader.visibility = View.VISIBLE
            tvReDemoRequest.visibility = View.VISIBLE
            demoRequestView.visibility = View.VISIBLE

            if (items.demo_request_id == "0") {
                tvReDemoRequest.text = "N/A"
            } else {
                tvReDemoRequest.text = items.demo_request_id
            }
        } else {
            txtDemoRequestHeader.visibility = View.GONE
            tvReDemoRequest.visibility = View.GONE
            demoRequestView.visibility = View.GONE
        }
        tvDdName.text =
            if (items.ddName == null || items.ddName.isEmpty()) "MySelf" else items.ddName
        tvReState.setText(items.state)
        tvReDistrict.setText(items.district)
        tvReTerritory.setText(items.territory)
        tvReTehsil.setText(items.tehsil)
        etRetVillage.setText(items.village)
        tvReProduct1.setText(items.product1)
        tvReCrop1.setText(items.crop1)
        etRetExpFarmer.setText(items.expectedFarmers.toString())
        etRetExpPDA.setText(items.expectedPda.toString())
        etRetParName.setText(items.spName1)
        etRetParNum.setText(items.spNumber1)
        /**
         * show image on the basis of data.!
         */
        if (!items.uploadedFile.isNullOrEmpty()) {
            ImgUrl = items.baseUrl + items.uploadedFile
            if (CheckConnectionStatus.isInternetAvailable(this@TicketDetailActivity)) {
                Glide.with(this).load(ImgUrl).into(img_src)
            }
            img_src.visibility = View.VISIBLE
        } else {
            if (items.tID != null)
                checkOfflineUpload(items.tID.toString())
        }

        if (items.spName2.isNullOrEmpty()) {
            tilParName2.visibility = View.GONE
        } else {
            tilParName2.visibility = View.VISIBLE
            etRetParName2.setText(items.spName2)
        }
        if (items.spNumber2.isNullOrEmpty()) {
            tilParNum2.visibility = View.GONE
        } else {
            tilParNum2.visibility = View.VISIBLE
            etRetParNum2.setText(items.spNumber2)
        }
        if (items.spName3.isNullOrEmpty()) {
            tilParName3.visibility = View.GONE
        } else {
            tilParName3.visibility = View.VISIBLE
            etRetParName3.setText(items.spName3)
        }
        if (items.spNumber3.isNullOrEmpty()) {
            tilParNum3.visibility = View.GONE
        } else {
            tilParNum3.visibility = View.VISIBLE
            etRetParNum3.setText(items.spNumber3)
        }
        if (items.spName4.isNullOrEmpty()) {
            tilParName4.visibility = View.GONE
        } else {
            tilParName4.visibility = View.VISIBLE
            etRetParName4.setText(items.spName4)
        }
        if (items.spNumber4.isNullOrEmpty()) {
            tilParNum4.visibility = View.GONE
        } else {
            tilParNum4.visibility = View.VISIBLE
            etRetParNum4.setText(items.spNumber4)
        }
        if (items.spName5.isNullOrEmpty()) {
            tilParName5.visibility = View.GONE
        } else {
            tilParName5.visibility = View.VISIBLE
            etRetParName5.setText(items.spName5)
        }
        if (items.spNumber5.isNullOrEmpty()) {
            tilParNum5.visibility = View.GONE
        } else {
            tilParNum5.visibility = View.VISIBLE
            etRetParNum5.setText(items.spNumber5)
        }
        if (items.spName6.isNullOrEmpty()) {
            tilParName6.visibility = View.GONE
        } else {
            tilParName6.visibility = View.VISIBLE
            etRetParName6.setText(items.spName6)
        }
        if (items.spNumber6.isNullOrEmpty()) {
            tilParNum6.visibility = View.GONE
        } else {
            tilParNum6.visibility = View.VISIBLE
            etRetParNum6.setText(items.spNumber6)
        }

        if (items.product2 == null || items.product2.isEmpty()) {
            llView2.visibility = View.GONE
        } else {
            llView2.visibility = View.VISIBLE
            tvReProduct2.setText(items.product2)
            tvReCrop2.setText(items.crop2)
        }
        if (items.product3 == null || items.product3.isEmpty()) {
            llView3.visibility = View.GONE
        } else {
            llView3.visibility = View.VISIBLE
            tvReProduct3.setText(items.product3)
            tvReCrop3.setText(items.crop3)
        }
    }

    override fun setItems(items: EndTicketBean) {
        hideProgress()
        if (items.status_code == 200) {
            makeToast(items.message)
            ivEditTicket.visibility = View.GONE
            btnRbEndTicket.visibility = View.GONE
            db.deleteAll()
            logAnalytics(false, items.message ?: "End ticket success", true)
            val intent = Intent()
            setResult(RESULT_OK, intent)
            finish()
        } else if (items.status_code == 409) {
            val preference: Preference = Preference.getInstance(this)
            preference.putString("ticketId", "")
            preference.putString("ticketTime", "")
            preference.putBolean("isLogin", false)
            preference.putString("token", "")
            logAnalytics(true, items.message ?: "End ticket fails", true)
            launchIntent(LoginActivity::class.java, true)
        } else {
            logAnalytics(true, items.message ?: "End ticket fails", true)
            makeToast(items.message)
        }
    }

    private fun endTicketAlert() {
        AlertDialog.Builder(this)
            .setTitle(resources.getString(R.string.btn_end_ticket))
            .setMessage(resources.getString(R.string.txt_end_ticket_comment))
            .setPositiveButton("Yes") { dialog, which ->
                val query = HashMap<String, Any>()
                query["api_token"] = Token
                ticket.tID?.let { query.put("tId", it) }
                if (CheckConnectionStatus.isInternetAvailable(this)) {
                    endTicketPresenter.onSubmitClick(query)
                    logEventTicketSelfParticipation("", true)
                } else {
                    closeTicketOffline()
                    logEventTicketSelfParticipation("offline close ticket", true)
                    makeToast(resources.getString(R.string.no_internet_connection))
                }
            }
            .setNegativeButton("No") { dialog, which -> }
            .show()
    }

    private fun closeTicketOffline() {
        Single.fromCallable {
            val appDatabase = DatabaseClient.getInstance(this).appDatabase
            appDatabase.ticketHistoryDao().closeTicket(ticket.id, true)
            appDatabase.ticketHistoryDao().updateTicketStatus(ticket.id, "INACTIVE")
            finish()
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun imageAddDialogAlert(msg: String) {
        AlertDialog.Builder(this)
            .setTitle(resources.getString(R.string.add_image))
            .setMessage(msg)
            .setPositiveButton("Yes") { dialog, which ->
                endTicketAlert()
            }
            .setNegativeButton("No") { dialog, which ->

            }
            .show()
    }


    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        easyImage?.handleActivityResult(
            requestCode,
            resultCode,
            data,
            this,
            object : DefaultCallback() {
                override fun onMediaFilesPicked(imageFiles: Array<MediaFile>, source: MediaSource) {
                    onPhotosReturned(imageFiles);
                }
            })
    }

    private fun onPhotosReturned(imageFiles: Array<MediaFile>) {
        if (imageFiles.isNotEmpty()) {
            ImagePickerUtil.onActivityResult2(10000, RESULT_OK, imageFiles[0])
        }
    }

    private fun updateImageUploadedInDB() {
        Single.fromCallable {
            val appDatabase = DatabaseClient.getInstance(this).appDatabase
            appDatabase.ticketHistoryDao().isImageUploaded(ticket.id, true)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
        }.subscribe()
    }

    override fun setItems(items: EditTicketBean) {

    }

    override fun setItems(items: String) {
        if (items.isNotEmpty()) {
            isImageUploaded = "1"
            ImgUrl = items
            if (CheckConnectionStatus.isInternetAvailable(this@TicketDetailActivity)) {
                Glide.with(this).load(ImgUrl).into(img_src)
                img_src.visibility = View.VISIBLE
            }
            dbUpdateModel.isUploaded = 1
            DatabaseManager.updateImageOfflineTable(this, dbUpdateModel)
            ticket.tID?.let { DatabaseManager.deleteAllImageOfflineTableParticular(this, it) }
            logEventTicketSelfParticipation(items, false)
            logAnalytics(false, items, false)
        } else {
            img_src.visibility = View.GONE
        }
    }

    override fun onFailure(items: String) {
        dbUpdateModel.lattitude = latitude.toString()
        dbUpdateModel.longitude = longtitude.toString()
        DatabaseManager.saveImageOfflineTable(
            this@TicketDetailActivity,
            dbUpdateModel,
            if (ticket.isOffline) 1 else 0
        )
        logEventTicketSelfParticipation(items, false)
        logAnalytics(true, items, false)
        makeToast(items)
    }

    private inner class LocationListener(provider: String) : android.location.LocationListener {
        init {
            mLastLocation = Location(provider)
        }

        override fun onLocationChanged(location: Location) {
            mLastLocation?.set(location)
            dbUpdateModel.lattitude = mLastLocation?.latitude.toString()
            dbUpdateModel.longitude = mLastLocation?.longitude.toString()
            isActiveLoader = false
            hideProgress()
            Log.e("dbUpdateModellocation", "lat-" + dbUpdateModel.lattitude)
            Log.e("dbUpdateModellocation", "long-" + dbUpdateModel.longitude)
            Log.e("location", "lat-" + mLastLocation?.latitude)
            Log.e("location", "long-" + mLastLocation?.longitude)
            preference.putString(Constants.latitude, mLastLocation?.latitude.toString())
            preference.putString(Constants.longitude, mLastLocation?.longitude.toString())
        }

        override fun onProviderDisabled(provider: String) {

        }

        override fun onProviderEnabled(provider: String) {

        }

        override fun onStatusChanged(provider: String, status: Int, extras: Bundle) {
        }
    }

    private fun getLocation() {
        checkPermission(
            arrayOf(Manifest.permission.ACCESS_FINE_LOCATION),
            object : PermissionListener {
                override fun onPermissionStatus(status: Int) {
                    if (status == com.dhanuka.indhan.Activity.Constants.PERMISSION_GRANTED)
                        onLocationPermissionGranted()
                    else if (status == com.dhanuka.indhan.Activity.Constants.PERMISSION_SET_NEVER_AGAIN)
                        showPermissionSettingDialog()
                }
            })
    }

    private fun onLocationPermissionGranted() {
        if (ActivityCompat.checkSelfPermission(
                this,
                Manifest.permission.ACCESS_FINE_LOCATION
            ) != PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(
                this,
                Manifest.permission.ACCESS_COARSE_LOCATION
            ) != PackageManager.PERMISSION_GRANTED
        ) {
            return
        }
        val loc = getSystemService(Context.LOCATION_SERVICE) as LocationManager
        if (!loc.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
            Toast.makeText(this, "Please Enable GPS.", Toast.LENGTH_LONG).show()
            return
        }
        fusedLocationClient.lastLocation
            .addOnSuccessListener(this) { location ->
                if (location != null) {
                    userLocation = location as Location?
                    latitude = userLocation?.latitude
                    longtitude = userLocation?.longitude
                } else {
                    userLocation = null
                    SmartLocation.with(this).location().start {
                        if (it != null) {
                            latitude = it.latitude
                            longtitude = it.longitude
                        }
                    }
                    Toast.makeText(
                        this,
                        "Not Able to Fetch Current Location. Try Again!!",
                        Toast.LENGTH_LONG
                    ).show()
                }
            }
    }

    private fun logEventTicketSelfParticipation(s: String, b: Boolean) {
        val model = LogModel()
        model.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
        val data =
            ("Ticket No : ${ticket.ticket} Ticket Data -> ${Gson().toJson(ticket)} " +
                    "${
                        if (!b) "Self Participation Data : ${Gson().toJson(dbUpdateModel)}"
                        else "End Ticket : ${ticket.tID}"
                    } ").trimIndent()
        model.apiData = data
        model.apiName =
            "Api Name : ${if (!b) "v2/ticketUpdateImage" else "v2/endTicket"} " +
                    "| ${if (TextUtils.isEmpty(s)) "Api Called" else s}"
        val userData = (preference.getString("user_id") + "_"
                + preference.getString("ecn") + "_"
                + preference.getString("user_type") + "_"
                + preference.getString("Username") + "_"
                + preference.getString("designation"))
        model.userData = "\nUSER DATA : -> $userData"
        writeLineInFile(Gson().toJson(model), preference.getString("ecn"))
    }

    private fun logAnalytics(b: Boolean, s: String, b1: Boolean) {
        val userData = (preference.getString("user_id") + "_"
                + preference.getString("ecn") + "_"
                + preference.getString("user_type") + "_"
                + preference.getString("Username") + "_"
                + preference.getString("designation"))
        val data =
            ("Ticket No : ${ticket.ticket} Ticket Data -> ${Gson().toJson(ticket)} " +
                    "${
                        if (!b1) "Self Participation Data : ${Gson().toJson(dbUpdateModel)}"
                        else "End Ticket : ${ticket.tID}"
                    } ").trimIndent()
        val bundle = HashMap<String, Any>()
        bundle[FirebaseAnalytics.Param.ITEM_ID] = preference.getString("user_id")
        bundle["Api_Name"] = if (!b1) "v2/ticketUpdateImage" else "v2/endTicket"
        bundle["user_detail"] = userData
        bundle["ticket_number"] = ticket.ticket
        bundle["api_data"] = data
        if (b) {
            bundle["error_message"] = s
        } else {
            bundle["message"] = s
        }
        logEvent("TicketDetail", "self_participation", bundle)
    }

}