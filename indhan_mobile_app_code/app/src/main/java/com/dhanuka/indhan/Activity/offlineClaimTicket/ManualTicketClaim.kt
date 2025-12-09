package com.dhanuka.indhan.Activity.offlineClaimTicket

import android.Manifest
import android.app.Activity
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.graphics.BitmapFactory
import android.location.Location
import android.location.LocationManager
import android.os.Build
import android.os.Bundle
import android.text.TextUtils
import android.view.ActionMode
import android.view.Menu
import android.view.MenuItem
import android.view.View
import android.widget.TextView
import android.widget.Toast
import androidx.activity.result.ActivityResult
import androidx.activity.result.contract.ActivityResultContracts.StartActivityForResult
import androidx.appcompat.app.AlertDialog
import androidx.core.app.ActivityCompat
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.R
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.ImagePickerUtil
import com.dhanuka.indhan.util.ImageUtils
import com.dhanuka.indhan.util.Utility
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import io.nlopez.smartlocation.SmartLocation
import kotlinx.android.synthetic.main.maunal_ticket_claim_activity.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import pl.aprilapps.easyphotopicker.DefaultCallback
import pl.aprilapps.easyphotopicker.EasyImage
import pl.aprilapps.easyphotopicker.MediaFile
import pl.aprilapps.easyphotopicker.MediaSource
import java.text.SimpleDateFormat
import java.util.*

//fromScreen == true when come from ticket history main menu

class ManualTicketClaim : BaseActivity() {

    private var dbUpdateModelDepart: ManualTicketClaimImage? = null
    private var imgUrl: String = ""
    private lateinit var fusedLocationClient: FusedLocationProviderClient
    private var latitude: Double? = null
    private var longtitude: Double? = null
    private var userLocation: Location? = null
    private lateinit var dbUpdateModel: ManualTicketClaimImage
    private lateinit var appDatabase: AppDatabase
    private var fromScreen = false
    private var isDeparture = false
    private var easyImage: EasyImage? = null
    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@ManualTicketClaim)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initCamera()
        setContentView(R.layout.maunal_ticket_claim_activity)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this)
        getLocation()
        fromScreen = intent?.getBooleanExtra("from_ticket", false) ?: false
        if (fromScreen) {
            tvTitle.text = "Manual Ticket Claim"
            edtFeedback.visibility = View.GONE
            rGpSelection.visibility = View.GONE
            dbUpdateModel = ManualTicketClaimImage()
        } else {
            isDeparture = intent?.getBooleanExtra("isDeparture", false) ?: false
            tvTitle.text = "Mark Self Participation " + if (isDeparture) "Departure" else "Arrival"
            edtFeedback.visibility = View.VISIBLE
            rGpSelection.visibility = View.GONE
            dbUpdateModel = ManualTicketClaimImage()
            dbUpdateModelDepart = ManualTicketClaimImage()
        }
        setTextWatcher()

    }

    private fun setTextWatcher() {
        imgClaimTicket.setOnClickListener(this)
        offlineManualData.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        txtSubmit.setOnClickListener(this)

        etTicketNumber.disableCopyPaste()
        etConfirmTicketNumber.disableCopyPaste()
    }

    private fun TextView.disableCopyPaste() {
        isLongClickable = false
        setTextIsSelectable(false)
        customSelectionActionModeCallback = object : ActionMode.Callback {
            override fun onCreateActionMode(mode: ActionMode?, menu: Menu): Boolean {
                return false
            }

            override fun onPrepareActionMode(mode: ActionMode?, menu: Menu): Boolean {
                return false
            }

            override fun onActionItemClicked(mode: ActionMode?, item: MenuItem): Boolean {
                return false
            }

            override fun onDestroyActionMode(mode: ActionMode?) {}
        }

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            customInsertionActionModeCallback = object : ActionMode.Callback {
                override fun onCreateActionMode(mode: ActionMode?, menu: Menu): Boolean {
                    return false
                }

                override fun onPrepareActionMode(mode: ActionMode?, menu: Menu): Boolean {
                    return false
                }

                override fun onActionItemClicked(mode: ActionMode?, item: MenuItem): Boolean {
                    return false
                }

                override fun onDestroyActionMode(mode: ActionMode?) {}
            }
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
                    userLocation = location
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

//    private fun copyImageToPublicGallery(context: Context, fileToCopy: File, folderName: String): String {
//        val bitmapToCopy = Files.getFixedRotationBitmap(fileToCopy)
//        val contentResolver = context.contentResolver
//        val contentValues = ContentValues()
//        contentValues.put(MediaStore.MediaColumns.DISPLAY_NAME, fileToCopy.name)
//        contentValues.put(MediaStore.MediaColumns.MIME_TYPE, "image/jpg")
//        contentValues.put(MediaStore.MediaColumns.RELATIVE_PATH, "${Environment.DIRECTORY_PICTURES}/$folderName")
//        val copyUri: Uri = contentResolver.insert(MediaStore.Images.Media.EXTERNAL_CONTENT_URI, contentValues)!!
//        val outputStream: OutputStream = contentResolver.openOutputStream(copyUri)!!
//        bitmapToCopy.compress(Bitmap.CompressFormat.JPEG, 100, outputStream)
//        outputStream.close()
//        Log.d(EASYIMAGE_LOG_TAG, "Copied image to public gallery: ${copyUri.path}")
//        return copyUri.path!!
//    }
//
//    private fun legacyCopyImageToPublicGallery(fileToCopy: File, folderName: String): String {
//        val bitmapToCopy = Files.getFixedRotationBitmap(fileToCopy)
//        val legacyExternalStorageDir = File(
//            Environment.getExternalStoragePublicDirectory(
//                Environment.DIRECTORY_PICTURES), folderName)
//        if (!legacyExternalStorageDir.exists()) legacyExternalStorageDir.mkdirs()
//        val copyFile = File(legacyExternalStorageDir, fileToCopy.name)
//        copyFile.createNewFile()
//        val outputStream = FileOutputStream(copyFile)
//        bitmapToCopy.compress(Bitmap.CompressFormat.JPEG, 100, outputStream)
//        outputStream.close()
//        Log.d(EASYIMAGE_LOG_TAG, "Copied image to public gallery: ${copyFile.path}")
//        return copyFile.path
//    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.imgClaimTicket -> {
                if (latitude == null || longtitude == null) {
                    getLocation()
                } else {
                    ImagePickerUtil.pickFromCamera2(
                        easyImage,
                        this@ManualTicketClaim,
                        { imageFile, _ ->
                            var file = ImageUtils.compressToExistingFile(
                                imageFile.absolutePath,
                                imageFile
                            )

                            if(file == null){
                                file = imageFile
                            }
                            val bitmap = BitmapFactory.decodeFile(file.path.toString())
                            if (bitmap != null) {
                                imgClaimTicket.setImageBitmap(bitmap)
                                imgUrl = file.path.toString()
                                if (!isDeparture) {
                                    dbUpdateModel.image_url_local = file.path.toString()
                                    dbUpdateModel.latitude = latitude.toString()
                                    dbUpdateModel.longitude = longtitude.toString()
                                    val cal = Calendar.getInstance( Locale.ENGLISH)
                                    val format1 = SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
                                    val formatted = format1.format(cal.time)
                                    dbUpdateModel.date = formatted
                                } else {
                                    dbUpdateModel.departure_imageURL = file.path.toString()
                                    dbUpdateModel.departure_latitude = latitude.toString()
                                    dbUpdateModel.departure_longitude = longtitude.toString()
                                    val cal = Calendar.getInstance( Locale.ENGLISH)
                                    val format1 = SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
                                    val formatted = format1.format(cal.time)
                                    dbUpdateModel.departure_time = formatted
                                }
                            }
                        },
                        ""
                    )
//                    .
//                    val takePictureIntent = Intent(this, CameraActivity::class.java)
//                    startForResult.launch(takePictureIntent)
                }
            }
            R.id.ivBack -> {
                onBackPressed()
            }
            R.id.offlineManualData -> {
                val intent = Intent(this, ManualTicketDetailsOfflineActivity::class.java)
                intent.putExtra("from_ticket", fromScreen)
                startForResultOffline.launch(intent)
            }

            R.id.txtSubmit -> {
                if (!fromScreen) {
                    if (!TextUtils.isEmpty(edtFeedback.text.toString().trim())) {
                        updateImageUploadedInDB()
                    } else {
                        makeToast("Please enter your feedback in box.")
                    }
                } else {
                    updateImageUploadedInDB()
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

    private fun updateImageUploadedInDB() {
        if (valid()) {
            val currentHitTimeTwoHour = System.currentTimeMillis()
            if (!isDeparture) {
                dbUpdateModel.ticket_number = etConfirmTicketNumber.text.toString().trim()
                if (!fromScreen) {
                    dbUpdateModel.description = edtFeedback.text.toString()
                }
                dbUpdateModel.isUploaded = 0
                dbUpdateModel.type = if (fromScreen) 1 else 2
                dbUpdateModel.lastHitTime = currentHitTimeTwoHour
            } else {
                GlobalScope.launch {
                    withContext(Dispatchers.IO) {
                        dbUpdateModelDepart = appDatabase.manualTicketClaimDao()
                            .getSingleManualTicketClaimData(
                                0,
                                etTicketNumber.text.toString().trim()
                            )
                            ?: ManualTicketClaimImage()
                    }.run {
                        if (dbUpdateModelDepart?.ticket_number != null) {
                            withContext(Dispatchers.Main) {
                                dbUpdateModel.id = dbUpdateModelDepart?.id ?: 0
                                dbUpdateModel.isUploaded = dbUpdateModelDepart?.isUploaded ?: 0
                                dbUpdateModel.image_url_local = dbUpdateModelDepart?.image_url_local
                                dbUpdateModel.latitude = dbUpdateModelDepart?.latitude
                                dbUpdateModel.longitude = dbUpdateModelDepart?.longitude
                                dbUpdateModel.ticket_number = dbUpdateModelDepart?.ticket_number
                                dbUpdateModel.description = dbUpdateModelDepart?.description
                                dbUpdateModel.date = dbUpdateModelDepart?.date
                                dbUpdateModel.lastHitTime = currentHitTimeTwoHour
                                dbUpdateModel.type = 3
                                dbUpdateModel.departure_comment = edtFeedback.text.toString()

                                val format1 = SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
                                val diff =
                                    Utility.findDifference(
                                        format1,
                                        dbUpdateModel.date,
                                        dbUpdateModel.departure_time
                                    )
                                dbUpdateModel.duration = "$diff"
                            }.run {
                                withContext(Dispatchers.IO) {
                                    if (isDeparture) {
                                        appDatabase.manualTicketClaimDao()
                                            .updateManualArrivalDepartureData(
                                                dbUpdateModel.id,
                                                dbUpdateModel.type,
                                                dbUpdateModel.departure_time,
                                                dbUpdateModel.departure_comment,
                                                dbUpdateModel.departure_latitude,
                                                dbUpdateModel.departure_longitude,
                                                dbUpdateModel.departure_imageURL,
                                                dbUpdateModel.duration
                                            )
                                        withContext(Dispatchers.Main) {
                                            val message =
                                                "Your arrival, departure data will be uploaded in background. Please check after sometime."
                                            AlertDialog.Builder(this@ManualTicketClaim)
                                                .setTitle("Arrival Departure")
                                                .setMessage(message)
                                                .setCancelable(false)
                                                .setPositiveButton(
                                                    "Okay"
                                                ) { dialog, _ ->
                                                    dialog.dismiss()
                                                    finish()
                                                }.show()
                                        }

                                    }
                                }
                            }
                        } else {
                            withContext(Dispatchers.Main) {
                                makeToast("Please enter arrival data for this ticket first.")
                            }
                        }
                    }
                }
            }

            GlobalScope.launch {
                withContext(Dispatchers.IO) {
                    if (!isDeparture) {
                        val data = appDatabase.manualTicketClaimDao()
                            .getSingleManualTicketClaimData(0, dbUpdateModel.ticket_number)
                        if (data == null) {
                            appDatabase.manualTicketClaimDao().insert(dbUpdateModel)
                            withContext(Dispatchers.Main) {
                                val message = if (fromScreen) {
                                    "You have successfully claimed this ticket manually. " +
                                            "This will be uploaded to server post verifying your presence in this activity by location of the photo captured."
                                } else {
                                    "Arrival data saved successfully."
                                }
                                AlertDialog.Builder(this@ManualTicketClaim).setTitle("Claim Ticket")
                                    .setMessage(message)
                                    .setCancelable(false)
                                    .setPositiveButton(
                                        "Okay"
                                    ) { dialog, _ ->
                                        dialog.dismiss()
                                        finish()
                                    }.show()
                            }
                        } else {
                            withContext(Dispatchers.Main) {
                                if (fromScreen) {
                                    makeToast("Ticket claim already done for this ticket number.")
                                } else {
                                    makeToast("Arrival data is already captured for this ticket number.")
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private fun valid(): Boolean {
        if (!TextUtils.isEmpty(etTicketNumber.text.toString().trim())) {
            if (!TextUtils.isEmpty(etConfirmTicketNumber.text.toString().trim())) {
                if (etTicketNumber.text.toString().trim()
                        .equals(etConfirmTicketNumber.text.toString().trim(), ignoreCase = true)
                ) {
                    if (!TextUtils.isEmpty(imgUrl)) {
                        return if (!fromScreen) {
                            if (!TextUtils.isEmpty(edtFeedback.text.toString().trim())) {
                                true
                            } else {
                                makeToast("Please enter your feedback.")
                                false
                            }
                        } else {
                            true
                        }
                    } else {
                        if (!fromScreen) {
                            makeToast("Please add image to mark self participation.")
                        } else {
                            makeToast("Please add image to claim ticket.")
                        }
                        return false
                    }
                } else {
                    makeToast("Ticket number and confirm ticket number are not same.")
                    return false
                }
            } else {
                makeToast("Please confirm ticket number.")
                return false
            }
        } else {
            makeToast("Please enter ticket number.")
            return false
        }
    }

    //Offline Activity Result
    private val startForResultOffline =
        registerForActivityResult(StartActivityForResult()) { result: ActivityResult ->
            when (result.resultCode) {
                Activity.RESULT_OK -> {

                }
            }
        }

}