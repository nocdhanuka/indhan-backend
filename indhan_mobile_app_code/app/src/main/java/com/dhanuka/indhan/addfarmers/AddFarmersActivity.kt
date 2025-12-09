package com.dhanuka.indhan.addfarmers

import android.Manifest
import android.app.Dialog
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.location.Location
import android.location.LocationManager
import android.os.Bundle
import android.os.Handler
import android.text.Editable
import android.text.TextWatcher
import android.view.View
import android.view.Window
import android.widget.ImageView
import android.widget.Toast
import androidx.core.app.ActivityCompat
import com.bumptech.glide.Glide
import com.bumptech.glide.load.engine.DiskCacheStrategy
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.Constants
import com.dhanuka.indhan.Activity.NewTicketMVP.CropSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Activity.NewTicketMVP.ProductSpinnerDialog
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.addfarmers.model.AddFarmerModel
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.*
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import com.google.gson.GsonBuilder
import io.nlopez.smartlocation.SmartLocation
import kotlinx.android.synthetic.main.activity_add_farmers.*
import okhttp3.MediaType
import okhttp3.MultipartBody
import okhttp3.RequestBody
import pl.aprilapps.easyphotopicker.DefaultCallback
import pl.aprilapps.easyphotopicker.EasyImage
import pl.aprilapps.easyphotopicker.MediaFile
import pl.aprilapps.easyphotopicker.MediaSource
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import rx.Single
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.io.File
import java.util.*
import kotlin.collections.HashMap

class AddFarmersActivity : BaseActivity(), AddFarmerContractor.AddFarmerView {
    override fun onClick(v: View?) {

    }

    override fun setItems(items: AddFarmerSuccessModel) {
//        makeToast(items.message)
//        finish()
    }

    override fun failure(items: String) {
//        makeToast(items)
    }

    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList()
    private lateinit var cropSpinnerDialog: CropSpinnerDialog
    private lateinit var cropSpinnerDialog1: CropSpinnerDialog
    private lateinit var cropSpinnerDialog2: CropSpinnerDialog

    private lateinit var productSpinnerDialog: ProductSpinnerDialog
    private lateinit var product2SpinnerDialog: ProductSpinnerDialog
    private lateinit var product3SpinnerDialog: ProductSpinnerDialog

    private val product1RecordResponseModel = ArrayList<ProductBean>()
    private val product2RecordResponseModel = ArrayList<ProductBean>()
    private val product3RecordResponseModel = ArrayList<ProductBean>()
    private var easyImage: EasyImage? = null

    private var crop1Id: Int = 0
    private var crop2Id: Int = 0
    private var crop3Id: Int = 0

    private var product1Id: Int = 0
    private var product2Id: Int = 0
    private var product3Id: Int = 0

    private var isCrop2 = false
    private var isCrop3 = false

    private var latitude: Double? = null
    private var longtitude: Double? = null
    private var userLocation: Location? = null
    private lateinit var fusedLocationClient: FusedLocationProviderClient

    private lateinit var presenterImpl: AddFarmerContractor.AddFarmerPresenter
    private lateinit var preference: Preference
    private var file: File? = null

    private val crop1RecordResponseModel = ArrayList<CropBean>()
    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@AddFarmersActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_add_farmers)
        initCamera()
        presenterImpl = AddfarmerPresenterImpl(this)
        preference = Preference(this)
        /*
        * fused location
        * */
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this)

        getLocation()


        updateCropList()
        /*
        * init the All three crop spinner list---------------------------------------------------------------------------
        * */
        cropSpinnerDialog =
            CropSpinnerDialog(this, crop1RecordResponseModel, "Select or Search Crop")
        cropSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerCrop1.text = item
                crop1Id = crop1RecordResponseModel[position].getcID()!!
                updateProductBeanList(crop1Id, 1)
            }
        })


        cropSpinnerDialog1 =
            CropSpinnerDialog(this, crop1RecordResponseModel, "Select or Search Crop")
        cropSpinnerDialog1.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerCrop2.text = item
                crop2Id = crop1RecordResponseModel[position].getcID()!!
                isCrop2 = true
                updateProductBeanList(crop2Id, 2)
            }
        })

        cropSpinnerDialog2 =
            CropSpinnerDialog(this, crop1RecordResponseModel, "Select or Search Crop")
        cropSpinnerDialog2.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerCrop3.text = item
                crop3Id = crop1RecordResponseModel[position].getcID()!!
                isCrop3 = true
                updateProductBeanList(crop3Id, 3)
            }
        })


        /*
        * init the all product list dialog--------------------------------------------------------------------------------
        * */
        productSpinnerDialog =
            ProductSpinnerDialog(this, product1RecordResponseModel, "Select or Search Product")
        productSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerProduct1.text = item
                product1Id = product1RecordResponseModel[position].getpID()!!
            }
        })

        product2SpinnerDialog =
            ProductSpinnerDialog(this, product1RecordResponseModel, "Select or Search Product")
        product2SpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerProduct2.text = item
                product2Id = product1RecordResponseModel[position].getpID()!!
            }
        })

        product3SpinnerDialog =
            ProductSpinnerDialog(this, product1RecordResponseModel, "Select or Search Product")
        product3SpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerProduct3.text = item
                product3Id = product1RecordResponseModel[position].getpID()!!
            }
        })



        tvReAddNew1.setOnClickListener {
            llView2.visibility = View.VISIBLE
            tvReAddNew1.visibility = View.GONE
            tvReAddNew2.visibility = View.VISIBLE
            isCrop2 = true
        }

        tvReAddNew2.setOnClickListener {
            llView3.visibility = View.VISIBLE
            tvReAddNew2.visibility = View.GONE
            isCrop3 = true
        }

        tvReRemove1.setOnClickListener {
            llView2.visibility = View.GONE
            tvReAddNew1.visibility = View.VISIBLE
            tvReAddNew2.visibility = View.GONE
            isCrop2 = false
        }
        tvReRemove2.setOnClickListener {
            llView3.visibility = View.GONE
            tvReAddNew2.visibility = View.VISIBLE
            isCrop3 = false
        }

        spinnerCrop1.setOnClickListener {
            if (!cropSpinnerDialog.isEnable)
                cropSpinnerDialog.showSpinerDialog()

        }
        spinnerCrop2.setOnClickListener {
            if (!cropSpinnerDialog1.isEnable)
                cropSpinnerDialog1.showSpinerDialog()

        }

        spinnerCrop3.setOnClickListener {
            if (!cropSpinnerDialog2.isEnable)
                cropSpinnerDialog2.showSpinerDialog()
        }

        spinnerProduct1.setOnClickListener {
            if (!productSpinnerDialog.isEnable)
                productSpinnerDialog.showSpinerDialog()
        }

        spinnerProduct2.setOnClickListener {
            if (!product2SpinnerDialog.isEnable)
                product2SpinnerDialog.showSpinerDialog()
        }

        spinnerProduct3.setOnClickListener {
            if (!product3SpinnerDialog.isEnable)
                product3SpinnerDialog.showSpinerDialog()
        }

        txtClick.setOnClickListener {
            if (latitude == null || longtitude == null) {
                getLocation()
            } else {
                PermissionsUtil.askPermissions(
                    this,
                    PermissionsUtil.CAMERA,
                    PermissionsUtil.STORAGE
                ) {
                    if (it) {
                        ImagePickerUtil.pickFromCamera2(
                            easyImage,
                            this@AddFarmersActivity,
                            { imageFile, tag ->
                                file = ImageUtils.compressToExistingFile(
                                    imageFile.absolutePath,
                                    imageFile
                                )
                                if(file == null){
                                    file = imageFile
                                }
                                imgFarmer.setImageBitmap(
                                    FileUtility.createBitmapFromFile(
                                        file
                                    )
                                )
                                imgCross.visibility = View.VISIBLE
                            },
                            ""
                        )

                    }
                }
            }
        }

        rlImgFarmer.setOnClickListener {
            if (latitude == null || longtitude == null) {
                getLocation()
            } else {
                if (file == null) {
                    PermissionsUtil.askPermissions(
                        this,
                        PermissionsUtil.CAMERA,
                        PermissionsUtil.STORAGE
                    ) {
                        if (it) {
                            ImagePickerUtil.pickFromCamera2(
                                easyImage,
                                this@AddFarmersActivity,
                                { imageFile, _ ->
                                    file = ImageUtils.compressToExistingFile(
                                        imageFile.absolutePath,
                                        imageFile
                                    )
                                    if(file == null){
                                        file = imageFile
                                    }
                                    imgFarmer.setImageBitmap(
                                        FileUtility.createBitmapFromFile(
                                            file
                                        )
                                    )
                                    imgCross.visibility = View.VISIBLE
                                    imgCamera.visibility = View.GONE
                                }, ""
                            )
                        }
                    }
                } else {
                    showdialog()
                }
            }
        }

        imgCross.setOnClickListener {
            file = null
            imgCross.visibility = View.GONE
            imgCamera.visibility = View.VISIBLE
            imgFarmer.setImageDrawable(null)

        }


        edtContactsNo.addTextChangedListener(object : TextWatcher {
            override fun afterTextChanged(s: Editable?) {
                println(s.toString())
                if (s != null) {
                    val stringData = s.toString().replace(" ", "")
                    if (s.contains(" ")) {
                        edtContactsNo.setText(stringData)
                        edtContactsNo.setSelection(stringData.length)
                        return
                    }
                    if (stringData.isNotEmpty()) {
                        if (stringData[0] == '0' || stringData[0] == '1' || stringData[0] == '2'
                            || stringData[0] == '3' || stringData[0] == '4' || stringData[0] == '5'
                        ) {
                            edtContactsNo.setText(
                                stringData[0].toString().replace(stringData[0].toString(), "")
                            )
                        }

                    }
                }
            }

            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {
            }

            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {

            }
        })

        edtAltContactsNO.addTextChangedListener(object : TextWatcher {
            override fun afterTextChanged(s: Editable?) {
                println(s.toString())
                if (s != null) {
                    var stringData = s.toString().replace(" ", "")
                    if (s.contains(" ")) {
                        edtAltContactsNO.setText(stringData)
                        edtAltContactsNO.setSelection(stringData.length)
                        return
                    }
                    if (stringData.isNotEmpty()) {
                        if (stringData[0] == '0' || stringData[0] == '1' || stringData[0] == '2'
                            || stringData[0] == '3' || stringData[0] == '4' || stringData[0] == '5'
                        ) {
                            edtAltContactsNO.setText(
                                stringData[0].toString().replace(stringData[0].toString(), "")
                            )
                        }

                    }
                }
            }

            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {
            }

            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {

            }
        })


        btnRbSubmit.setOnClickListener {
            if (latitude == null || longtitude == null) {
                getLocation()
                Toast.makeText(
                    this,
                    "Unable to fetch Current Location. Try Again!!",
                    Toast.LENGTH_LONG
                ).show()
            } else {
                if (validationList()) {
                    btnRbSubmit.isEnabled = false
                    val addFarmerModel = AddFarmerModel()
                    addFarmerModel.ECN = preference.getString("ecn")
                    addFarmerModel.apiToken = preference.getString("token")
                    addFarmerModel.villageId = intent.getStringExtra("villageId")
                    addFarmerModel.farmerName = edtFullName.text.toString()
                    addFarmerModel.fatherName = edtFatherName.text.toString()
                    addFarmerModel.contactnumber = edtContactsNo.text.toString()
                    addFarmerModel.alternateContactnumber = edtAltContactsNO.text.toString()
                    addFarmerModel.farmerLat = latitude.toString()
                    addFarmerModel.farmerLong = longtitude.toString()
                    addFarmerModel.filePath = file!!.absolutePath
                    addFarmerModel.fileName = file?.name

                    val map = HashMap<String, RequestBody>()
                    map["ECN"] = getRequestBody(preference.getString("ecn"))
                    map["apiToken"] = getRequestBody(preference.getString("token"))
                    map["villageId"] = getRequestBody(intent.getStringExtra("villageId") ?: "")

                    map["farmerName"] = getRequestBody(edtFullName.text.toString())
                    map["fatherName"] = getRequestBody(edtFatherName.text.toString())
                    map["contactnumber"] = getRequestBody(edtContactsNo.text.toString())
                    map["alternateContactnumber"] = getRequestBody(edtAltContactsNO.text.toString())

                    if (isCrop2 && isCrop3) {
                        map["cropId"] = getRequestBody("$crop1Id,$crop2Id,$crop3Id")
                        map["acres"] =
                            getRequestBody("${edtAcres1.text},${edtAcres2.text},${edtAcres3.text}")
                        addFarmerModel.cropId = "$crop1Id,$crop2Id,$crop3Id"
                        addFarmerModel.acres =
                            "${edtAcres1.text},${edtAcres2.text},${edtAcres3.text}"
                    } else if (isCrop2) {
                        map["cropId"] = getRequestBody("$crop1Id,$crop2Id")
                        map["acres"] = getRequestBody("${edtAcres1.text},${edtAcres2.text}")
                        addFarmerModel.cropId = "$crop1Id,$crop2Id"
                        addFarmerModel.acres = "${edtAcres1.text},${edtAcres2.text}"
                    } else if (isCrop3) {
                        map["cropId"] = getRequestBody("$crop1Id,$crop3Id")
                        map["acres"] = getRequestBody("${edtAcres1.text},${edtAcres3.text}")
                        addFarmerModel.cropId = "$crop1Id,$crop3Id"
                        addFarmerModel.acres = "${edtAcres1.text},${edtAcres3.text}"
                    } else {
                        map["cropId"] = getRequestBody("$crop1Id")
                        map["acres"] = getRequestBody("${edtAcres1.text}")
                        addFarmerModel.cropId = "$crop1Id"
                        addFarmerModel.acres = "${edtAcres1.text}"
                    }
                    map["farmerLat"] = getRequestBody(latitude.toString())
                    map["farmerLong"] = getRequestBody(longtitude.toString())


                    val fileBody1 = RequestBody.create(MediaType.parse("image/*"), file!!)
                    val imagePart1: MultipartBody.Part =
                        MultipartBody.Part.createFormData("farmerImage", file?.name, fileBody1)
                    if (CheckConnectionStatus.isInternetAvailable(this)) {
                        showProgress()
                        val okHttpClient = RetrofitClient.httpClient
                        val mGson = GsonBuilder().setLenient().create()
                        val builder: Retrofit.Builder = Retrofit.Builder()
                            .baseUrl(RetrofitClient.LIVE_URL)
                            .client(okHttpClient)
                            .addConverterFactory(GsonConverterFactory.create(mGson))
                        val retrofit = builder.build()
                        val client: ApiServiceRequest =
                            retrofit.create(ApiServiceRequest::class.java)
                        val call: Call<AddFarmerSuccessModel> = client.addFarmerNew(map, imagePart1)
                        call.enqueue(object : Callback<AddFarmerSuccessModel?> {
                            override fun onResponse(
                                call: Call<AddFarmerSuccessModel?>,
                                response: Response<AddFarmerSuccessModel?>
                            ) {
                                hideProgress()
                                if (call.isCanceled) {
                                    insertAddFarmerInDB(addFarmerModel)
                                    makeToast("Slow Internet Connection. Your Data is Saved Locally.")
                                } else {
                                    makeToast(response.body()?.message ?: response.message())
                                }
                                if (response.code() == 200)
                                    finish()
                            }

                            override fun onFailure(
                                call: Call<AddFarmerSuccessModel?>,
                                t: Throwable
                            ) {
                                hideProgress()
                                if (call.isCanceled) {
                                    insertAddFarmerInDB(addFarmerModel)
                                    makeToast("Slow Internet Connection. Your Data will be automatically synced.")
                                    finish()
                                } else
                                    makeToast("Failure")
                            }
                        })
                        Handler().postDelayed({
                            call.cancel()
                        }, 5000)
                    } else {
                        makeToast("No Internet Connection. Data is saved Locally.")
                        Handler().postDelayed({ finish() }, 1000)
                        insertAddFarmerInDB(addFarmerModel)
                    }
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

    private fun insertAddFarmerInDB(addFarmerModel: AddFarmerModel) {
        Single.fromCallable {
            val appDatabase = DatabaseClient.getInstance(this).appDatabase
            appDatabase.addFarmerDao().insert(addFarmerModel)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun updateCropList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
        DatabaseManager.getProductCropDetailProductNew(this)?.forEach {
            val bean = CropBean()
            bean.setcID(it.cropId.toInt())
            bean.item = it.crop
            if (!checkValue(bean.getcID()!!))
                crop1RecordResponseModel.add(bean)
        }
    }

    private fun showdialog() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_image)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(true)
        val msg = dialog.findViewById<View>(R.id.imgView) as ImageView
        val requestOptions = RequestOptions()
            .diskCacheStrategy(DiskCacheStrategy.NONE)
            .skipMemoryCache(true)

        Glide.with(this)
            .load(file?.absoluteFile)
            .apply(requestOptions)
            .into(msg)
        dialog.show()
    }

    private fun checkValue(id: Int): Boolean {
        product1RecordResponseModel.forEach {
            if (id == it.getpID()) {
                return true
            }
        }
        return false
    }

    private fun updateProductBeanList(id: Int, position: Int) {
        val temp = ArrayList<ProductBean>()
        prodCropResponseModel.forEach {
            if (it.cropId.equals(id.toString())) {
                val product = ProductBean()
                product.setpID(it.productId.toInt())
                product.item = it.product
                temp.add(product)
            }
        }
        when (position) {
            1 -> {
                product1RecordResponseModel.clear()
                product1RecordResponseModel.addAll(temp)
                productSpinnerDialog.updatelist(product1RecordResponseModel)
            }
            2 -> {
                product2RecordResponseModel.clear()
                product2RecordResponseModel.addAll(temp)
                product2SpinnerDialog.updatelist(product2RecordResponseModel)
            }
            3 -> {
                product3RecordResponseModel.clear()
                product3RecordResponseModel.addAll(temp)
                product3SpinnerDialog.updatelist(product3RecordResponseModel)
            }
        }
    }

    private fun getRequestBody(value: String): RequestBody {
        return RequestBody.create(MediaType.parse("application/json; charset=utf-8"), value)
    }

    private fun validationList(): Boolean {
        if (edtFullName.text.toString() == "") {
            makeToast("Please Enter Full Name")
            return false
        } else if (edtFatherName.text.toString() == "") {
            makeToast("Please Enter Father Name")
            return false
        } else if (edtContactsNo.text.toString() == "") {
            makeToast("Please Enter Contact No.")
            return false
        } else if (edtContactsNo.text.toString() == "") {
            makeToast("Please Enter Contact No.")
            return false
        } else if (edtContactsNo.text.toString().length < 10 || edtContactsNo.text.toString().length > 10) {
            makeToast("Please Enter Valid Contact No.")
            return false
        } else if (edtAltContactsNO.text.toString() == "") {
            makeToast("Please Enter Alternate Contact No.")
            return false
        } else if (edtAltContactsNO.text.toString().length < 10 || edtAltContactsNO.text.toString().length > 10) {
            makeToast("Please Enter Valid  Alternate Contact No.")
            return false
        } else if (edtContactsNo.text.toString() == edtAltContactsNO.text.toString()) {
            makeToast("Contacts No. and Alternate Contacts No. should not be same")
            return false
        } else if (crop1Id == 0) {
            makeToast("Please Select Crop Name")
            return false
        } else if (edtAcres1.text.toString() == "") {
            makeToast("Please Enter acres")
            return false
        } else if (isCrop2) {
            when {
                crop2Id == 0 -> {
                    makeToast("Please Select Crop Name")
                    return false
                }
                edtAcres2.text.toString() == "" -> {
                    makeToast("Please Enter acres")
                    return false
                }
            }
            if (file == null) {
                makeToast("Please Click a picture ")
                return false
            }
        } else if (isCrop3) {
            when {
                crop3Id == 0 -> {
                    makeToast("Please Select Crop Name")
                    return false
                }
                edtAcres3.text.toString() == "" -> {
                    makeToast("Please Enter acres")
                    return false
                }
            }
            if (file == null) {
                makeToast("Please Click a picture ")
                return false
            }
        } else if (file == null) {
            makeToast("Please Click a picture ")
            return false
        } else if (latitude == null) {
            getLocation()
            return false
        }
        return true
    }

    private fun getLocation() {
        checkPermission(
            arrayOf(Manifest.permission.ACCESS_FINE_LOCATION),
            object : PermissionListener {
                override fun onPermissionStatus(status: Int) {
                    if (status == Constants.PERMISSION_GRANTED)
                        onLocationPermissionGranted()
                    else if (status == Constants.PERMISSION_SET_NEVER_AGAIN)
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
                        println("AddFarmersActivity.onLocationPermissionGranted : ${it.latitude}  ${it.longitude}")
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
}
