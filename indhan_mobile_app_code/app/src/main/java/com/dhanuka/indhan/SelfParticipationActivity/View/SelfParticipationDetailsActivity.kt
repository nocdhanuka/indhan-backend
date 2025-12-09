package com.dhanuka.indhan.PlanATP.View

import android.app.Activity
import android.app.Dialog
import android.content.Context
import android.content.DialogInterface
import android.content.Intent
import android.graphics.Bitmap
import android.graphics.BitmapFactory
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.location.Location
import android.location.LocationManager
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.provider.Settings
import android.util.Log
import android.view.View
import android.view.Window
import androidx.appcompat.app.AlertDialog
import com.bumptech.glide.Glide
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ImageOfflineModel
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.ActivateTicketAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.SelfParticipationActivity.Model.TicketList
import com.dhanuka.indhan.util.ImagePickerUtil
import com.dhanuka.indhan.util.ImageUtils
import com.himangi.imagepreview.ImagePreviewActivity
import kotlinx.android.synthetic.main.activity_sp_detail.*
import pl.aprilapps.easyphotopicker.DefaultCallback
import pl.aprilapps.easyphotopicker.EasyImage
import pl.aprilapps.easyphotopicker.MediaFile
import pl.aprilapps.easyphotopicker.MediaSource
import java.io.File


/**
 * Created by John on 5/26/2018.
 */
class SelfParticipationDetailsActivity : BaseActivity() {

    lateinit var preference: Preference
    lateinit var token: String
    lateinit var adapter: ActivateTicketAdapter
    lateinit var model: TicketList
    var ImgUrl = ""
    var isActive = true
    var imgfile: File? = null
    var isCameraOn = false
    var isUpdating = false
    var mActivity: Activity? = null
    private var easyImage: EasyImage? = null
    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@SelfParticipationDetailsActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_sp_detail)
        initCamera()
        val bundle = intent.extras
        if (bundle != null) {
            model = bundle.getSerializable("ticketDetails") as TicketList
            isActive = bundle.getBoolean("isactive")
        }
        hideSoftKeyboard()

        if (!isActive) {
            camera_ini.visibility = View.GONE
            layout.visibility = View.GONE
        } else {
            camera_ini.visibility = View.VISIBLE
            layout.visibility = View.VISIBLE
        }

        if (!model.uploadedFile.isNullOrEmpty()) {
            ImgUrl = model.baseUrl + model.uploadedFile
        }
        setUpView()
        // presenter = SelfParticipationDetailPresenterImpl(this)
        preference = Preference(this)
        token = preference.getString("token")
        iCross.setOnClickListener(this)
        camera_ini.setOnClickListener(this)
        camerabtn.setOnClickListener(this)
        activateTicketBtn.setOnClickListener(this)
    }

    override fun onResume() {
        super.onResume()
        mActivity = this
    }

    override fun onDestroy() {
        super.onDestroy()
        mActivity = null
    }

    private fun setUpView() {
        tv_crop.text = getCrop()
        tv_product.text = getProduct()
        // tv_ds.text = if (model.dataSource.isNullOrEmpty()) "- -" else model.dataSource
        tv_ds.text = if (model.dataSource.isNullOrEmpty()) "- -" else model.dataSource
        if (model.tehsil.isNullOrEmpty())
            tv_tehsil.text = "- -"
        else
            tv_tehsil.text = model.tehsil
        if (model.village.isNullOrEmpty())
            tv_village.text = "- -"
        else
            tv_village.text = model.village
        if (model.ticket.isNullOrEmpty())
            ticket.text = "- -"
        else
            ticket.text = model.ticket
        if (model.name.isNullOrEmpty())
            bdo.text = "- -"
        else
            bdo.text = model.name
        if (isActive) {
            tilVilage.visibility = View.VISIBLE
            layout_desc.visibility = View.GONE
            if (!model.description.isNullOrEmpty())
                tvMeDescription.setText(model.description)
        } else {
            tilVilage.visibility = View.GONE
            layout_desc.visibility = View.VISIBLE
            if (model.description.isNullOrEmpty())
                tv_desc.setText("- -")
            else
                tv_desc.setText(model.description)
        }
        if (!model.uploadedFile.isNullOrEmpty()) {
            if (isActive) {
                layout.visibility = View.VISIBLE
                activateTicketBtn.text = "Already Submitted \n (Click to submit again) "
            }
            layout_img.visibility = View.VISIBLE
            Glide.with(this).load(model.baseUrl + model.uploadedFile).into(camerabtn)

        } else {
            checkOfflineUpload()
        }

    }

    private fun checkOfflineUpload() {
        var imageModel = DatabaseManager.getNonUploadedImageOfflineTable(this, model.tid.toString())
        if (imageModel != null) {
            imgfile = File(imageModel.image_url_local)
            var bitmap = BitmapFactory.decodeFile(imgfile.toString())
            bitmapMJ = bitmap
            //   var bitmap = BitmapFactory.decodeFile(dataString.toString())
            if (bitmap != null) {
                layout_img.visibility = View.VISIBLE
                camerabtn.setImageBitmap(bitmap)
                layout.visibility = View.VISIBLE
                activateTicketBtn.text = "Already Submitted \n (Click to submit again) "
            } else {
                layout.visibility = View.VISIBLE
                layout_img.visibility = View.GONE
            }

            if (imageModel.description.isNullOrEmpty())
                tvMeDescription.setText("- -")
            else
                tvMeDescription.setText(imageModel.description)
        } else {
            layout_img.visibility = View.GONE
            tvMeDescription.setText("- -")
        }
    }

    private fun getProduct(): String {
        var result = ""
        if (!model.product1.isNullOrEmpty())
            result = model.product1
        if (!model.product2.isNullOrEmpty())
            result += ", " + model.product2
        if (!model.product3.isNullOrEmpty())
            result += ", " + model.product3
        if (result.isNullOrEmpty())
            return "- -"
        else return result
    }

    private fun getCrop(): String {
        var result = ""
        if (!model.crop1.isNullOrEmpty())
            result = model.crop1
        if (!model.crop2.isNullOrEmpty())
            result += ", " + model.crop2
        if (!model.crop3.isNullOrEmpty())
            result += ", " + model.crop3
        if (result.isNullOrEmpty())
            return "- -"
        else return result
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.iCross -> {
                finish()
            }
            R.id.activateTicketBtn -> {
                if (!isUpdating)
                    uploadTicket()
                // showdialog("testing")
            }
            R.id.camera_ini -> {
                initiateLocationLogin()
            }
            R.id.camerabtn -> {
                var intent = Intent(this, ImagePreviewActivity::class.java)
                if (ImgUrl.length > 5) {
                    val imageList: ArrayList<String> = ArrayList()
                    imageList.add(ImgUrl)
                    intent.putExtra(ImagePreviewActivity.IMAGE_LIST, imageList);
                    intent.putExtra(ImagePreviewActivity.CURRENT_ITEM, 0);
                    startActivity(intent)
                }
            }
        }
    }

    var isActiveLoader = false

    var dbUpdateModel = ImageOfflineModel()
    private fun uploadTicket() {
        val result = validateValues()
        if (result == "perfect") {
            ImgUrl = imgfile.toString()
            dbUpdateModel.description = tvMeDescription.text.toString()
            dbUpdateModel.image_url_local = imgfile.toString()
            dbUpdateModel.ticket_id = model.tid.toString()
            dbUpdateModel.isUploaded = 0
            dbUpdateModel.type = Constants.IS_SELF
            dbUpdateModel.lattitude = mLastLocation?.latitude.toString()
            dbUpdateModel.longitude = mLastLocation?.longitude.toString()
            DatabaseManager.saveImageOfflineTable(
                this@SelfParticipationDetailsActivity,
                dbUpdateModel,
                0
            )
            showdialog()
        } else
            makeToast(result)
    }


    private fun validateValues(): String {
        if (imgfile != null) {
            if (imgfile.toString().isNullOrEmpty() && bitmapMJ == null)
                return "Please capture Image"
        } else {
            return "Please capture Image"
        }
        if (bitmapMJ == null)
            return "Please capture Image"
        if (tvMeDescription.text.toString().isNullOrEmpty())
            return "Please Enter Description"
        return "perfect"
    }

    var bitmapMJ: Bitmap? = null

//    @RequiresApi(Build.VERSION_CODES.KITKAT)
//    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
//        super.onActivityResult(requestCode, resultCode, data)
//
//        if (resultCode == 101) {
//            if (data!!.data != null) {
//                val dataString: Uri = data!!.data!!
//                val path = File(dataString.path)
//
//            } else makeToast("Camera not working properly. Please contact the Technical Team")
//        }
//    }


    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        isCameraOn = false
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


    private fun openPicture() {
        if (!isCameraOn) {
            isCameraOn = true
            ImagePickerUtil.pickFromCamera2(
                easyImage,
                this@SelfParticipationDetailsActivity,
                { imageFile, _ ->
                    val file = ImageUtils.compressToExistingFile(
                        imageFile.absolutePath,
                        imageFile
                    )
                    val bitmap = BitmapFactory.decodeFile(file.path.toString())
                    bitmapMJ = bitmap
                    if (bitmap != null) {
                        layout_img.visibility = View.VISIBLE
                        camerabtn.setImageBitmap(bitmap)
                        imgfile = file
                        getLocation()
                        isActiveLoader = true
                        Handler(Looper.getMainLooper()).postDelayed({
                            if (isActiveLoader) {
                                makeToast("Couldn't fetch the location")
                                hideProgress()
                            }
                        }, 3000)
                    } else {
                        layout_img.visibility = View.GONE
                    }
                },
                ""
            )
//                .
//            val takePictureIntent = Intent(this, CameraActivity::class.java)
//            startActivityForResult(takePictureIntent, 101)
        }
    }


    private fun showdialog() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_ticket_number)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val ticketNumber = dialog.findViewById<View>(R.id.tvMeTicketNumber) as Medium
        val msg = dialog.findViewById<View>(R.id.msg) as Medium
        val tic_banner = dialog.findViewById<View>(R.id.tic_banner) as Medium
        ticketNumber.visibility = View.GONE
        tic_banner.visibility = View.GONE
        msg.text = "Successfully Posted"
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
            finish()
        }
        if (mActivity != null)
            dialog.show()
    }

    fun initiateLocationLogin() {
        val lm = getSystemService(Context.LOCATION_SERVICE) as LocationManager
        var gps_enabled = false
        var network_enabled = false

        try {
            gps_enabled = lm.isProviderEnabled(LocationManager.GPS_PROVIDER)
        } catch (ex: Exception) {
        }


        try {
            network_enabled = lm.isProviderEnabled(LocationManager.NETWORK_PROVIDER)
        } catch (ex: Exception) {
        }


        if (!gps_enabled && !network_enabled) {
            // notify user
            val dialog = AlertDialog.Builder(this)
            dialog.setMessage("Before Clicking picture! Please enable your GPS")
            dialog.setPositiveButton(
                "Open Location Setting",
                object : DialogInterface.OnClickListener {
                    override fun onClick(paramDialogInterface: DialogInterface, paramInt: Int) {
                        val myIntent = Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS)
                        startActivity(myIntent)
                    }
                })
            dialog.setNegativeButton("Cancel", object : DialogInterface.OnClickListener {

                override fun onClick(paramDialogInterface: DialogInterface, paramInt: Int) {
                    paramDialogInterface.dismiss()
                }
            })
            dialog.show()
        } else {
            openPicture()
        }
    }


    private fun initializeLocationManager() {
        if (mLocationManager == null) {
            mLocationManager =
                applicationContext.getSystemService(Context.LOCATION_SERVICE) as LocationManager
        }
    }

    var mLastLocation: Location? = null

    private inner class LocationListener(provider: String) : android.location.LocationListener {

        init {
            mLastLocation = Location(provider)
        }

        override fun onLocationChanged(location: Location) {
            mLastLocation?.set(location)
            isActiveLoader = false
            hideProgress()

            dbUpdateModel.lattitude = mLastLocation?.latitude.toString()
            dbUpdateModel.longitude = mLastLocation?.longitude.toString()
            Log.e("location", "lat-" + dbUpdateModel.lattitude)
            Log.e("location", "long-" + dbUpdateModel.longitude)
            // DatabaseManager.saveImageOfflineTable(this@SelfParticipationDetailsActivity, dbUpdateModel)
        }

        override fun onProviderDisabled(provider: String) {
        }

        override fun onProviderEnabled(provider: String) {
        }

        override fun onStatusChanged(provider: String, status: Int, extras: Bundle) {
        }
    }

    private var mLocationListeners = arrayOf(
        LocationListener(LocationManager.GPS_PROVIDER),
        LocationListener(LocationManager.NETWORK_PROVIDER)
    )

    private var mLocationManager: LocationManager? = null
    private val LOCATION_INTERVAL: Long = 5000
    private val LOCATION_DISTANCE = 10f
    fun getLocation() {
        showProgress()
        initializeLocationManager()
        try {
            mLocationManager?.requestLocationUpdates(
                LocationManager.NETWORK_PROVIDER, LOCATION_INTERVAL, LOCATION_DISTANCE,
                mLocationListeners[1]
            )
        } catch (ex: java.lang.SecurityException) {
            Log.i("john", "fail to request location update, ignore", ex)
        } catch (ex: IllegalArgumentException) {
            Log.d("john", "network provider does not exist, " + ex.message)
        }

        try {
            mLocationManager?.requestLocationUpdates(
                LocationManager.GPS_PROVIDER, LOCATION_INTERVAL, LOCATION_DISTANCE,
                mLocationListeners[0]
            )
        } catch (ex: java.lang.SecurityException) {
            Log.i("john", "fail to request location update, ignore", ex)
        } catch (ex: IllegalArgumentException) {
            Log.d("john", "gps provider does not exist " + ex.message)
        }
    }
}