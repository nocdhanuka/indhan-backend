package com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration

import android.Manifest
import android.app.Activity
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.graphics.BitmapFactory
import android.location.Location
import android.location.LocationManager
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.text.TextUtils
import android.view.View
import android.view.inputmethod.InputMethodManager
import android.widget.ImageView
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.core.app.ActivityCompat
import androidx.recyclerview.widget.DefaultItemAnimator
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.adapter.AddIfcFarmerAdapter
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.AddIfcFarmer
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.*
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import com.google.firebase.analytics.FirebaseAnalytics
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken
import io.nlopez.smartlocation.SmartLocation
import kotlinx.android.synthetic.main.activity_add_ifc_farmer.*
import org.joda.time.DateTime
import pl.aprilapps.easyphotopicker.DefaultCallback
import pl.aprilapps.easyphotopicker.EasyImage
import pl.aprilapps.easyphotopicker.MediaFile
import pl.aprilapps.easyphotopicker.MediaSource
import rx.Single
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.io.File
import java.io.Serializable
import java.text.SimpleDateFormat
import java.util.*
import kotlin.math.roundToInt

class AddIfcFarmerRegistrationActivity : BaseActivity(), View.OnClickListener,
    AddIfcFarmerAdapter.AddIfcFarmerClickListener {
    private var totalPda: Double = 0.0
    private var preference: Preference? = null
    private var easyImage: EasyImage? = null
    private var listOfString: ArrayList<String> = ArrayList()
    private var ticketNumber = ""
    private var appDatabase: AppDatabase? = null
    private var longitude: Double = 0.0
    private var latitude: Double = 0.0
    private var userLocation: Location? = null
    private lateinit var fusedLocationClient: FusedLocationProviderClient
    private var file: File? = null
    private val mAddIfcFarmerModelList = LinkedList<AddIfcFarmer>()
    private lateinit var mAdapter: AddIfcFarmerAdapter
    internal lateinit var simpleDateFormat: SimpleDateFormat
    private lateinit var serverDateFormat: SimpleDateFormat
    private val uniqueIfcFarmerRecordModel = ArrayList<AddIfcFarmer>()
    private var linkedHashSet = LinkedHashSet<AddIfcFarmer>()
    private var message: String = "Farmer are less than 8, Are you sure you want to submit?"

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.tvAddNewFarmer -> {
                hideKeyboardFrom(this, tvAddNewFarmer)
                var someIndexIsEmpty = false
                val listOfUsed = ArrayList<String>()
                mAddIfcFarmerModelList.forEach { data ->
                    if (data.mobile != null) {
                        if (!listOfUsed.contains(data.mobile))
                            listOfUsed.add(data.mobile)
                    }
                }
                for (indexArray in mAddIfcFarmerModelList.indices) {
                    if (TextUtils.isEmpty(mAddIfcFarmerModelList[indexArray].farmer_name?.trim())) {
                        makeToast("Please enter farmer name")
                        someIndexIsEmpty = true
                        break
                    }
                    if (mAddIfcFarmerModelList[indexArray].mobile?.trim().isNullOrEmpty()) {
                        makeToast("Please enter the Phone Number")
                        someIndexIsEmpty = true
                        break
                    }
                    if (listOfString.size > 0) {
                        if (listOfString.contains(mAddIfcFarmerModelList[indexArray].mobile?.trim())) {
                            makeToast("Farmer phone number already exists in this ticket.")
                            someIndexIsEmpty = true
                            break
                        }
                    }
                    if (mAddIfcFarmerModelList[indexArray].mobile?.trim()?.length != 10) {
                        makeToast("Please enter correct Phone Number")
                        someIndexIsEmpty = true
                        break
                    }
                    if (TextUtils.isEmpty(mAddIfcFarmerModelList[indexArray].acrers)) {
                        makeToast("Please enter acerage for the farmer.")
                        someIndexIsEmpty = true
                        break
                    }
                    if (TextUtils.isEmpty(mAddIfcFarmerModelList[indexArray].image)) {
                        makeToast("Please take farmer image.")
                        someIndexIsEmpty = true
                        break
                    }
                    if (mAddIfcFarmerModelList[indexArray].latitude == 0.0) {
                        makeToast("Please enable your location and try again.")
                        someIndexIsEmpty = true
                        break
                    }

                    if (listOfUsed.size > 0) {
                        if (listOfUsed.size != mAddIfcFarmerModelList.size) {
                            makeToast("Farmer phone number can not be duplicate, please remove duplicate numbers.")
                            someIndexIsEmpty = true
                            break
                        }
                    }
                }
                if (!someIndexIsEmpty) {
                    mAddIfcFarmerModelList.add(
                        mAddIfcFarmerModelList.size,
                        createEmptyAddNumberModel()
                    )
                }
                mAdapter.notifyItemChanged(mAddIfcFarmerModelList.size)
            }

            R.id.ivSaveFarmers -> {
                hideKeyboardFrom(this, ivSaveFarmers)

                val listOfUsed = ArrayList<String>()
                mAddIfcFarmerModelList.forEach { data ->
                    if (!listOfUsed.contains(data.mobile))
                        listOfUsed.add(data.mobile)
                }
                var someIndexIsEmpty = false
                for (indexArray in mAddIfcFarmerModelList.indices) {
                    if (TextUtils.isEmpty(mAddIfcFarmerModelList[indexArray].farmer_name?.trim())) {
                        makeToast("Please enter farmer name")
                        someIndexIsEmpty = true
                        break
                    }
                    if (mAddIfcFarmerModelList[indexArray].mobile?.trim().isNullOrEmpty()) {
                        makeToast("Please enter the Phone Number")
                        someIndexIsEmpty = true
                        break
                    }
                    if (listOfString.size > 0) {
                        if (listOfString.contains(mAddIfcFarmerModelList[indexArray].mobile?.trim())) {
                            makeToast("Farmer phone number already exists in this ticket.")
                            someIndexIsEmpty = true
                            break
                        }
                    }
                    if (mAddIfcFarmerModelList[indexArray].mobile?.trim()?.length != 10) {
                        makeToast("Please enter correct Phone Number")
                        someIndexIsEmpty = true
                        break
                    }
                    if (TextUtils.isEmpty(mAddIfcFarmerModelList[indexArray].acrers)) {
                        makeToast("Please enter acerage for the farmer.")
                        someIndexIsEmpty = true
                        break
                    }
                    if (TextUtils.isEmpty(mAddIfcFarmerModelList[indexArray].image)) {
                        makeToast("Please take farmer image.")
                        someIndexIsEmpty = true
                        break
                    }
                    if (mAddIfcFarmerModelList[indexArray].latitude == 0.0) {
                        makeToast("Please enable your location and try again.")
                        someIndexIsEmpty = true
                        break
                    }
                    if (listOfUsed.size > 0) {
                        if (listOfUsed.size != mAddIfcFarmerModelList.size) {
                            makeToast("Farmer phone number can not be duplicate, please remove duplicate numbers.")
                            someIndexIsEmpty = true
                            break
                        }
                    }
                }
                if (!someIndexIsEmpty) {
                    var allGoneWell = false
                    val newRecordResponseModelList = ArrayList<AddIfcFarmer>()
                    for (indexOfArray in mAddIfcFarmerModelList.indices) {
                        if (validateFields(indexOfArray)) {
                            newRecordResponseModelList.add(mAddIfcFarmerModelList[indexOfArray])
                        } else {
                            break
                        }
                    }
                    if (newRecordResponseModelList.size == 0) {
                        makeToast("There is no new farmer added, please add new farmers.")
                    } else if (newRecordResponseModelList.size != 0) {
                        allGoneWell = true
                    }
                    if (allGoneWell) {
                        linkedHashSet.clear()
                        newRecordResponseModelList.forEach() {
                            val itr = linkedHashSet.iterator()
                            var isNumberAlreadyInList = false
                            while (itr.hasNext()) {
                                if (itr.next().mobile.trim() == it.mobile.trim()) {
                                    isNumberAlreadyInList = true
                                }
                            }
                            if (!isNumberAlreadyInList) {
                                if (it == newRecordResponseModelList.first()) {
                                    it.setActivityDistance(0.0)
                                } else {
                                    val index = newRecordResponseModelList.indexOf(it)
                                    val disCal = distanceBetweenLatLng(
                                        newRecordResponseModelList[index - 1].latitude.toFloat(),
                                        newRecordResponseModelList[index - 1].longitude.toFloat(),
                                        newRecordResponseModelList[index].latitude.toFloat(),
                                        newRecordResponseModelList[index].longitude.toFloat(),
                                    ).toDouble()
                                    it.setActivityDistance(disCal)
                                }
                                linkedHashSet.add(it)
                            }
                            saveDataInDataBase(it)
                        }
                    }
                    uniqueIfcFarmerRecordModel.clear()
                    uniqueIfcFarmerRecordModel.addAll(0, linkedHashSet)
                    submitIfcFarmerData()
                }
            }

            R.id.ivCross -> finish()
        }
    }

    private fun alertDialog() {
        AlertDialog.Builder(this).setTitle("Register IFC Farmer")
            .setMessage(message)
            .setCancelable(false)
            .setPositiveButton(
                "Got It"
            ) { _, _ -> uploadData() }.show()
    }

    private fun submitIfcFarmerData() {
        totalPda = 0.0
        val allDataToSync = ArrayList<AddIfcFarmer>()
        uniqueIfcFarmerRecordModel.forEach() {
            if (it.status == 0) {
                allDataToSync.add(it)
            }
            totalPda += Utility.roundOffDecimal((it.acrers ?: "0").toDouble())
        }
//        saveActivityDuration(
//            uniqueIfcFarmerRecordModel.first(),
//            uniqueIfcFarmerRecordModel.last()
//        )
        if (uniqueIfcFarmerRecordModel.size < 8) {
            message = "This activity may not get qualified as number of farmers are less than 8."
            alertDialog()
        } else if (uniqueIfcFarmerRecordModel.size >= 10 && totalPda.roundToInt() < 50) {
            message = "This activity may not get qualified as PDA is less than 50."
            alertDialog()
        } else {
            uploadData()
        }
    }

    private fun uploadData() {
        Single.fromCallable {
            val data = appDatabase?.ifcFarmerDao()?.getSingleIfcFarmerData(
                status = 0,
                isuploaded = false
            )
            if (data != null) {
                runOnUiThread {
                    if (CheckConnectionStatus.isInternetAvailable(this)) {
                        showProgress()
                        Handler(Looper.getMainLooper()).postDelayed({
                            makeToast("Your data will be uploaded in background. Please check after sometime.")
                            runOnUiThread() {
                                hideProgress()
                            }
                            val resultCode = 101
                            val resultIntent = Intent()
                            resultIntent.putExtra(
                                "UNIQUE_NUMBERS_RECORDS",
                                uniqueIfcFarmerRecordModel as Serializable
                            )
                            setResult(resultCode, resultIntent)
                            finish()
                        }, 1800)
                    } else {
                        val resultCode = 101
                        val resultIntent = Intent()
                        resultIntent.putExtra(
                            "UNIQUE_NUMBERS_RECORDS",
                            uniqueIfcFarmerRecordModel as Serializable
                        )
                        setResult(resultCode, resultIntent)
                        finish()
                        makeToast("Internet connection not available. Hence data is saved offline and gets sync when you are back online.")
                    }
                }
            } else {
                hideProgress()
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
        }.subscribe()
    }

    private fun getLocalDateTime(imageDateTime: String?): String {
        val output: SimpleDateFormat
        val data = try {
            val sdf = SimpleDateFormat("yyyy-MM-dd hh:mm a")
            output = SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
            sdf.parse(imageDateTime!!)
        } catch (e: Exception) {
            return ""
        }
        return output.format(data!!)
    }

    private fun hideKeyboardFrom(context: Context, view: View) {
        val imm: InputMethodManager =
            context.getSystemService(Activity.INPUT_METHOD_SERVICE) as InputMethodManager
        imm.hideSoftInputFromWindow(view.windowToken, 0)
    }

    private fun validateFields(indexArray: Int): Boolean {
        if (TextUtils.isEmpty(mAddIfcFarmerModelList[indexArray].farmer_name?.trim())) {
            makeToast("Please enter farmer name at $indexArray")
            return false
        }
        if (mAddIfcFarmerModelList[indexArray].mobile?.trim().isNullOrEmpty()) {
            makeToast("Please enter the Phone Number at $indexArray")
            return false
        }
        if (mAddIfcFarmerModelList[indexArray].mobile?.trim()?.length != 10) {
            makeToast("Please enter correct Phone Number at $indexArray")
            return false
        }
        if (TextUtils.isEmpty(mAddIfcFarmerModelList[indexArray].acrers)) {
            makeToast("Please enter acreage for the farmer at $indexArray")
            return false
        }
        if (TextUtils.isEmpty(mAddIfcFarmerModelList[indexArray].image)) {
            makeToast("Please take farmer image at $indexArray")
            return false
        }
        if (mAddIfcFarmerModelList[indexArray].latitude == 0.0) {
            makeToast("Please enable your location and try again at $indexArray")
            return false
        }
        return true
    }

    private fun saveDataInDataBase(addIfcFarmer: AddIfcFarmer) {
        Single.fromCallable {
            appDatabase?.ifcFarmerDao()?.insert(addIfcFarmer)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
        }.subscribe()
    }

    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@AddIfcFarmerRegistrationActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_add_ifc_farmer)
        preference = Preference.getInstance(this)
        initCamera()
        getIntentData()
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this)
        appDatabase = DatabaseClient.getInstance(this@AddIfcFarmerRegistrationActivity).appDatabase
        ticketNumber = intent?.getStringExtra("ticket_no") ?: ""
        getLocation()
        tvAddNewFarmer.setOnClickListener(this)
        ivSaveFarmers.setOnClickListener(this)
        ivCross.setOnClickListener(this)
        rvListIfcFarmer.itemAnimator =
            DefaultItemAnimator()
        mAdapter =
            AddIfcFarmerAdapter(
                mAddIfcFarmerModelList, this,
                this@AddIfcFarmerRegistrationActivity
            )
        rvListIfcFarmer.adapter = mAdapter
        addData()
    }

    private fun getIntentData() {
        intent?.getStringExtra("listOfData")?.let {
            val myType = object : TypeToken<List<AddIfcFarmer>>() {}.type
            val list = Gson().fromJson<List<AddIfcFarmer>>(it, myType)
            listOfString.clear()
            list?.forEach { data -> listOfString.add(data.mobile) }
        }
    }

    private fun addData() {
        if (mAddIfcFarmerModelList.size == 0) {
            mAddIfcFarmerModelList.add(0, createEmptyAddNumberModel())
        }
        mAdapter.notifyItemChanged(mAddIfcFarmerModelList.size)
    }

    private fun createEmptyAddNumberModel(): AddIfcFarmer {
        val addNumberModel = AddIfcFarmer()
        addNumberModel.status = 0
        addNumberModel.farmer_name = ""
        addNumberModel.serverId = DateTime.now().toDateTime().millis.toInt()
        addNumberModel.mobile = ""
        addNumberModel.acrers = ""
        addNumberModel.image = null
        addNumberModel.imageName = ""
        addNumberModel.ticket_no = ""
        addNumberModel.latitude = 0.0
        addNumberModel.longitude = 0.0
        addNumberModel.setActivityDistance(0.0)
        addNumberModel.image_date_time = "0"
        addNumberModel.serverTime = "0"
        addNumberModel.uploaded = false
        return addNumberModel
    }

    private fun getLocalTime(): String {
        val calendar = Calendar.getInstance(Locale.ENGLISH)
        simpleDateFormat = SimpleDateFormat("yyyy-MM-dd hh:mm a", Locale.ENGLISH)
        return simpleDateFormat.format(calendar.time)
    }

    private fun getServerTime(): String {
        val calendar = Calendar.getInstance(Locale.ENGLISH)
        serverDateFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH)
        return serverDateFormat.format(calendar.time)
    }

    override fun onClickImage(view: View, addIfcFarmerModel: AddIfcFarmer, position: Int) {
        if (latitude == 0.0 && longitude == 0.0) {
            getLocation()
        } else {
            PermissionsUtil.askPermissions(this, PermissionsUtil.CAMERA, PermissionsUtil.STORAGE) {
                if (it) {
                    ImagePickerUtil.pickFromCamera2(
                        easyImage,
                        this@AddIfcFarmerRegistrationActivity, { imageFile, _ ->
                            file = ImageUtils.compressToExistingFile(
                                imageFile.absolutePath,
                                imageFile
                            )
                            if(file == null){
                                file = imageFile
                            }
                            if (!TextUtils.isEmpty(file?.path)) {
                                mAddIfcFarmerModelList[position].image = file?.path
                                mAddIfcFarmerModelList[position].ticket_no = ticketNumber
                                mAddIfcFarmerModelList[position].imageName = file?.name
                                mAddIfcFarmerModelList[position].latitude = latitude
                                mAddIfcFarmerModelList[position].longitude = longitude
                                mAddIfcFarmerModelList[position].image_date_time = getLocalTime()
                                mAddIfcFarmerModelList[position].serverTime = getServerTime()
                                (view as ImageView).setImageBitmap(
                                    BitmapFactory.decodeFile(
                                        imageFile?.absolutePath
                                    )
                                )
                                mAdapter.notifyItemChanged(mAddIfcFarmerModelList.size)
                            }
                        }, ""
                    )
                }
            }
        }
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

    override fun onDeleteRow(position: Int) {
        if (position != -1) {
            logEventCustom("from local add screen")
            mAddIfcFarmerModelList.remove(mAddIfcFarmerModelList[position])
            if (mAdapter != null)
                mAdapter.notifyDataSetChanged()
        }
    }

    private fun logEventCustom(s: String) {
        val firebaseAnalytics = FirebaseAnalytics.getInstance(this)
        val bundle = Bundle()
        bundle.putString(
            "user_detail", preference?.getString("user_id")
                .plus("_${preference?.getString("ecn")}")
                .plus("_${preference?.getString("user_type")}")
                .plus(
                    "_${preference?.getString("Username")}"
                        .plus("_${preference?.getString("designation")}")
                )
        )
        bundle.putString(FirebaseAnalytics.Param.CONTENT_TYPE, "ifc delete local")
        bundle.putString("delete_local", s)
        bundle.putString(FirebaseAnalytics.Param.ITEM_ID, preference?.getString("user_id"))
        bundle.putString(FirebaseAnalytics.Param.ITEM_NAME, "deleted_ifc_farmer_local")
        bundle.putString("uploaded_from", "add ifc farmer")
        firebaseAnalytics.logEvent("DELETE_FARMER", bundle)
    }

    fun getLocation() {
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
                    userLocation = location
                    latitude = userLocation?.latitude ?: 0.0
                    longitude = userLocation?.longitude ?: 0.0
                } else {
                    userLocation = null
                    SmartLocation.with(this).location().start {
                        if (it != null) {
                            latitude = it.latitude
                            longitude = it.longitude
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

    override fun onBackPressed() {
        var someIndexIsEmpty = false
        for (indexArray in mAddIfcFarmerModelList.indices) {
            if (TextUtils.isEmpty(mAddIfcFarmerModelList[indexArray].farmer_name?.trim())
                && mAddIfcFarmerModelList[indexArray].mobile?.trim().isNullOrEmpty()
                && mAddIfcFarmerModelList[indexArray].mobile?.trim()?.length != 10
                && TextUtils.isEmpty(mAddIfcFarmerModelList[indexArray].acrers)
                && TextUtils.isEmpty(mAddIfcFarmerModelList[indexArray].image)
                && mAddIfcFarmerModelList[indexArray].latitude == 0.0
            ) {
                someIndexIsEmpty = false
            } else {
                showDialog(
                    "You have unsaved IFC farmers. This will get deleted if you go back.\n" +
                            "Press OK to go back!"
                )
                someIndexIsEmpty = true
                break
            }
        }
        if (!someIndexIsEmpty) {
            super.onBackPressed()
        }
    }

    private fun showDialog(message: String) {
        AlertDialog.Builder(this).setTitle("Register IFC Farmer")
            .setMessage(message)
            .setCancelable(false)
            .setPositiveButton(
                "Okay"
            ) { dialog, _ ->
                dialog.cancel()
                finish()
            }
            .setNegativeButton(
                "cancel"
            ) { dialog, _ -> dialog.cancel() }.show()
    }

    private fun distanceBetweenLatLng(
        lat_a: Float,
        lng_a: Float,
        lat_b: Float,
        lng_b: Float
    ): Float {
        val startPoint = Location("locationA")
        startPoint.latitude = lat_a.toDouble()
        startPoint.longitude = lng_a.toDouble()
        val endPoint = Location("locationB")
        endPoint.latitude = lat_b.toDouble()
        endPoint.longitude = lng_b.toDouble()
        val distance: Double = startPoint.distanceTo(endPoint).toDouble()
        return distance.toFloat()
    }

}