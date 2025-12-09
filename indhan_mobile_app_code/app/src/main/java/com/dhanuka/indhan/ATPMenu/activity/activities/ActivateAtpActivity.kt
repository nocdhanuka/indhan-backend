package com.dhanuka.indhan.ATPMenu.activity.activities

import android.Manifest
import android.app.Dialog
import android.content.Intent
import android.content.pm.PackageManager
import android.location.Location
import android.os.Bundle
import android.util.Log
import android.view.View
import android.view.ViewGroup
import android.view.WindowManager
import android.widget.Toast
import androidx.core.app.ActivityCompat
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.ATPBean
import com.dhanuka.indhan.ATPMenu.activity.adapters.AtpActivateAdapter
import com.dhanuka.indhan.ATPMenu.activity.adapters.InvitedFarmerAdapter
import com.dhanuka.indhan.ATPMenu.activity.dialogs.DemoChooseDialog
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.ATPMenu.activity.models.ValidatedfarmerListResponse
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.*
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_activate_atp.*
import kotlinx.android.synthetic.main.dialog_close_ticket.*
import okhttp3.MediaType
import okhttp3.MultipartBody
import okhttp3.RequestBody
import pl.aprilapps.easyphotopicker.DefaultCallback
import pl.aprilapps.easyphotopicker.EasyImage
import pl.aprilapps.easyphotopicker.MediaFile
import pl.aprilapps.easyphotopicker.MediaSource
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.io.File
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*

class ActivateAtpActivity : BaseActivity() {

    private lateinit var fusedLocationClient: FusedLocationProviderClient
    private var latitude: Double? = null
    private var longtitude: Double? = null
    private var userLocation: Location? = null
    var atpBean: ArrayList<ATPBean.Data.Product> = ArrayList()
    var inviFarmerList: ArrayList<ValidatedfarmerListResponse.Data> = ArrayList()
    var planId: String = ""
    var ticketId: String = ""

    private var file: File? = null

    private lateinit var preference: Preference
    private var easyImage: EasyImage? = null

    override fun onClick(v: View?) {
    }

    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@ActivateAtpActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_activate_atp)
        initCamera()
        preference = Preference(this)
        if (intent.hasExtra(Constants.ATP_DETAIL)) {
            val data: ATPBean.Data =
                intent.getSerializableExtra(Constants.ATP_DETAIL) as ATPBean.Data
            setDataOnDetail(data)
        }
        //apply click on view
        applyClickOnView()

        /*
        * fused location
        * */
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this)
        getLocation()

    }

    //apply click on view
    private fun applyClickOnView() {
        txtChooseDemoNo.setOnClickListener {
            DemoChooseDialog(this@ActivateAtpActivity, planId, object : OnSuccess {
                override fun onClick(data: String) {
                    if (data.equals("yes")) {
                        txtStatus.text = "Active"
                        llRegisterFarmerCloseTicket.visibility = View.VISIBLE
                        llChooseDemoInviteFarmer.visibility = View.GONE
                    }
                }

            }).show()
        }

        //click on register farmer
        txtRegisterFarmer.setOnClickListener {
            var intent = Intent(this, RegisterFarmerActivity::class.java)
            intent.putExtra(Constants.PLAN_ATP_ID, planId)
            intent.putExtra(Constants.TICKET_ID, ticketId)
            startActivity(intent)
        }
        txtCloseTicket.setOnClickListener {
            showCloseAtpDialog()
        }
        //invite farmer
        txtAddInvitedFarmers.setOnClickListener {
            var intent = Intent(this, InviteFarmerActivity::class.java)
            intent.putExtra(Constants.PLAN_ATP_ID, planId)
            startActivity(intent)
        }
    }

    private fun setDataOnDetail(data: ATPBean.Data) {
        if (data.status.equals("0")) {
            txtCurrentStatus.visibility = View.GONE

        } else if (data.status.equals("1")) {
            txtCurrentStatus.visibility = View.VISIBLE
            txtCurrentStatus.text = "Activated"
        } else if (data.status.equals("2")) {
            txtCurrentStatus.visibility = View.VISIBLE
            txtCurrentStatus.text = "Closed"


        }
        ticketId = data.ticket_id
        planId = data.plan_atp_id
        txtATPName.text = "ATP: " + data.plan_atp_id

        if (data.status == "1") {
            txtStatus.text = "Active"

        } else {
            txtStatus.text = ""
            llRegisterFarmerCloseTicket.visibility = View.GONE
            llChooseDemoInviteFarmer.visibility = View.VISIBLE
            txtView.visibility = View.GONE
            txtAddInvitedFarmers.visibility = View.GONE
        }

        //get difference between two date
        val currentDate = getCurrentDate()
        val getPlanDate = data.plan_date.split("T").toTypedArray()

        val getPlanTime = getPlanDate[0]

        val simpleDateFormat = SimpleDateFormat("yyyy-MM-dd")
        val date1 = simpleDateFormat.parse(currentDate)
        val date2 = simpleDateFormat.parse(getPlanTime)
        val diff: Long = TimeStamp.printDifference(date1, date2)
        if (currentDate.equals(getPlanTime) && data.status == "1") {
            llRegisterFarmerCloseTicket.visibility = View.VISIBLE
            llChooseDemoInviteFarmer.visibility = View.GONE
        } else if (diff >= 0 && diff <= 2 && data.status == "1") {
            llRegisterFarmerCloseTicket.visibility = View.GONE
            llChooseDemoInviteFarmer.visibility = View.VISIBLE

        } else {
            txtChooseDemoNo.visibility = View.VISIBLE
            txtView.visibility = View.GONE
            txtAddInvitedFarmers.visibility = View.GONE
        }


        //set current month name
        txtCurrentMonthName.text = getTime(getPlanTime)
        atpBean.clear()
        atpBean.addAll(data.product)
        if (atpBean.size > 0) {
            txtNotFound.visibility = View.GONE
        } else {
            txtNotFound.visibility = View.VISIBLE
        }

        // Creates a vertical Layout Manager
        rvAtpList.layoutManager =
            LinearLayoutManager(this@ActivateAtpActivity)
        val adapter = AtpActivateAdapter(this@ActivateAtpActivity, atpBean)
        rvAtpList.adapter = adapter
    }

    override fun onResume() {
        super.onResume()
        inviteFarmerListApi()
    }

    public fun getLocation() {
        PermissionsUtil.askPermission(this, PermissionsUtil.LOCATION) {
            if (it) {
                onLocationPermissionGranted()
            }
        }
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
        fusedLocationClient.lastLocation
            .addOnSuccessListener(this) { location ->
                if (location != null) {
                    userLocation = location as Location?
                    latitude = userLocation?.latitude
                    longtitude = userLocation?.longitude
                    Log.e("latitudeLongi", "" + latitude + " " + longtitude + "")
                } else {
                    userLocation = null
                }
            }
    }


    override fun showProgress() {
        super.showProgress()
    }

    override fun hideProgress() {
        super.hideProgress()
    }

    //show dialog
    fun showCloseAtpDialog(): Unit {
        val dialog = Dialog(this)
        dialog.window!!.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN)
        dialog.window!!.attributes.windowAnimations = R.style.PauseDialogAnimation
        dialog.setContentView(R.layout.dialog_close_ticket)
        dialog.window!!.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.WRAP_CONTENT
        )

        dialog.window!!.setBackgroundDrawableResource(android.R.color.transparent)

        dialog.imgCloseActiveAtp.setOnClickListener {
            PermissionsUtil.askPermissions(this, PermissionsUtil.CAMERA, PermissionsUtil.STORAGE) {
                if (it) {
                    ImagePickerUtil.selectImage2(
                        easyImage,
                        this@ActivateAtpActivity,
                        { imageFile, _ ->
                            dialog.icPhoto.visibility = View.GONE
                            file = ImageUtils.compressToExistingFile(
                                imageFile.absolutePath,
                                imageFile
                            )
                            if (file == null) {
                                file = imageFile
                            }
                            dialog.imgCloseActiveAtp.setImageBitmap(
                                FileUtility.createBitmapFromFile(
                                    file
                                )
                            )
                        },
                        ""
                    )
                }
            }
        }
        dialog.txtSubmit.setOnClickListener {
            if (validateForm()) {
                if (CheckConnectionStatus.isInternetAvailable(this)) {
                    closeTicketApi()
                } else {
                    makeToast(resources.getString(R.string.no_internet_connection))
                }
            }
        }
        dialog.show()
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
                    onPhotosReturned(imageFiles, source)
                }
            })
    }

    private fun onPhotosReturned(imageFiles: Array<MediaFile>, source: MediaSource) {
        if (imageFiles.isNotEmpty()) {
            if (source == MediaSource.CAMERA_IMAGE) {
                ImagePickerUtil.onActivityResult2(10000, RESULT_OK, imageFiles[0])
            } else {
                ImagePickerUtil.onActivityResult2(20000, RESULT_OK, imageFiles[0])
            }
        }
    }

    fun validateForm(): Boolean {
        if (file == null) {
            makeToast("Please upload picture")
            return false
        }
        return true
    }

    internal fun getCurrentDate(): String {
        val c = Calendar.getInstance(Locale.ENGLISH).time
        println("Current time => $c")

        val df = SimpleDateFormat("yyyy-MM-dd")
        val formattedDate = df.format(c)
        return formattedDate
    }

    //close ticket api
    private fun closeTicketApi() {
        showProgress()
        var model: String = preference.getString("token")
        val query = java.util.HashMap<String, RequestBody>()
        query.put("apiToken", getRequestBody(model))
        query.put("plan_atp_id", getRequestBody(planId))
        query.put("status", getRequestBody("2"))
        val fileBody1 = RequestBody.create(MediaType.parse("image/*"), file!!)
        val imagePart1: MultipartBody.Part =
            MultipartBody.Part.createFormData("image", file?.name, fileBody1)
        val gson = Gson()
        val json = gson.toJson(query)
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .closeAtpApi(query, imagePart1).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonAtpResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    Toast.makeText(
                        this@ActivateAtpActivity,
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.",
                        Toast.LENGTH_LONG
                    ).show()
                }

                override fun onNext(commonBean: CommonAtpResponse) {
                    hideProgress()
                    if (commonBean.status_code == 200) {
                        makeToast(commonBean.message)
                        finish()
                    }
                }
            })
    }

    //invite farmer api
    private fun inviteFarmerListApi() {
        showProgress()
        var model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("plan_atp_id", planId)

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .validateFarlerListApi(query).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ValidatedfarmerListResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(commonBean: ValidatedfarmerListResponse) {
                    hideProgress()
                    if (commonBean.statusCode == 200) {
                        inviFarmerList.clear()
                        inviFarmerList.addAll(commonBean.data)
                        if (inviFarmerList.size > 0) {
                            llInvitedFarmer.visibility = View.VISIBLE
                        } else {
                            llInvitedFarmer.visibility = View.GONE

                        }
                        setInvitedFarmerList(inviFarmerList)
                    }
                }
            })
    }

    private fun setInvitedFarmerList(data: ArrayList<ValidatedfarmerListResponse.Data>) {

        // Creates a vertical Layout Manager
        rvInvitedFarmerList.layoutManager =
            LinearLayoutManager(this@ActivateAtpActivity)
        var adapter = InvitedFarmerAdapter(this@ActivateAtpActivity, data)
        rvInvitedFarmerList.adapter = adapter

    }
    //end

    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<out String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        PermissionsUtil.onRequestPermissionsResult(requestCode, permissions, grantResults)
    }

    private fun getRequestBody(value: String): RequestBody {
        return RequestBody.create(MediaType.parse("application/json; charset=utf-8"), value)
    }

    internal fun getTime(date: String): String {
        val finalTime = date
        var date1: Date? = null
        val sdf = SimpleDateFormat("yyyy-MM-dd")
        try {
            date1 = sdf.parse(date)
        } catch (e: ParseException) {
            e.printStackTrace()
        }

        val timeStr: String
        val inputFormat = SimpleDateFormat("dd-MMM-yyyy")
        timeStr = inputFormat.format(date1)
        return timeStr
    }

}
