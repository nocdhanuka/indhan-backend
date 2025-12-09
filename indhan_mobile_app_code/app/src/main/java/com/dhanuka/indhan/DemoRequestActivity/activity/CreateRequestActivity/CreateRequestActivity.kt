package com.dhanuka.indhan.DemoRequestActivity.activity.CreateRequestActivity

import android.Manifest
import android.app.DatePickerDialog
import android.app.Dialog
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.graphics.Color
import android.graphics.Typeface
import android.graphics.drawable.ColorDrawable
import android.location.Location
import android.location.LocationManager
import android.os.Bundle
import android.os.Handler
import android.text.TextUtils
import android.view.View
import android.view.ViewGroup
import android.view.Window
import android.view.WindowManager
import android.widget.ImageView
import android.widget.Toast
import androidx.core.app.ActivityCompat
import com.bumptech.glide.Glide
import com.bumptech.glide.load.engine.DiskCacheStrategy
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.*
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.DemoRequestActivity.model.*
import com.dhanuka.indhan.DemoRequestActivity.models.*
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.*
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.Connectivity
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.*
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.gson.Gson
import com.google.gson.GsonBuilder
import io.nlopez.smartlocation.SmartLocation
import kotlinx.android.synthetic.main.activity_create_request.*
import kotlinx.android.synthetic.main.dialog_date_picker.*
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
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.io.File
import java.util.*
import kotlin.collections.ArrayList


class CreateRequestActivity : BaseActivity() {
    private lateinit var appDatabase: AppDatabase
    lateinit var crop: String
    lateinit var productName: String
    lateinit var farmerName: String
    lateinit var state: String
    lateinit var districtName: String
    lateinit var pinCode: String
    lateinit var tahsil: String
    lateinit var contactNo: String
    lateinit var village: String
    lateinit var showingDate: String
    lateinit var spray: String
    var cropId = ""
    var productId = ""
    var stateId = ""
    var districtId = ""
    var tahsilId = ""
    var villageId = ""
    var farmerType = "new"
    private var easyImage: EasyImage? = null
    private lateinit var cropSpinnerDialog: CropSpinnerDialogNew
    private lateinit var cropProductSpinnerDialog: CropProductSpinnerDialog

    private lateinit var stateSpinnerDialog: StateSpinnerDialogNew
    private lateinit var districtSpinnerDialog: DistrictSpinnerDialogNew
    private lateinit var tahsilSpinnerDialog: TahsilSpinnerDialogNew
    private lateinit var villagelSpinnerDialog: AllVillageSpinnerDialog
    private lateinit var spraySpinnerDialog: SpraySpinnerDialogNew

    private val crop1RecordResponseModel = ArrayList<CropBean>()
    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList()
    private var listState: ArrayList<StateData> = ArrayList()
    private var cropProductList: ArrayList<CropProductData> = ArrayList()
    private var distTrictList: ArrayList<DistrictDataNew> = ArrayList()
    private var tahsilList: ArrayList<TehsilDataNew> = ArrayList()
    private var villageList: ArrayList<AllVillageListResponse.Data> = ArrayList()
    private var sprayList: ArrayList<SprayModel> = ArrayList()
    private var latitude: Double? = null
    private var longtitude: Double? = null
    private var userLocation: Location? = null
    private lateinit var fusedLocationClient: FusedLocationProviderClient
    var selectedDate = ""
    var isFromExisting = "no"
    var imageUrl = ""

    private lateinit var preference: Preference
    private var file: File? = null

    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@CreateRequestActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_create_request)
        preference = Preference(this)
        initCamera()
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        //apply click on view
        applyClickOnView()
        //get crop productListData
        updateCropList()
        //get state date
        if (Connectivity.isConnected(this@CreateRequestActivity)) {
            getStateList()
        } else {
            Single.fromCallable {
                listState.clear()
                val stateRecordResponseModel =
                    appDatabase.stateDao().allStateList as ArrayList<AllStateBean.StateData>
                stateRecordResponseModel.forEach {
                    val data = StateData(it.state, it.state_id)
                    listState.add(data)
                }
            }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

            }.subscribe()
        }
        fusedLocationClient = FusedLocationProviderClient(this)

        getLocation()
        if (intent.hasExtra(Constants.FROM_EXISTING)) {
            isFromExisting = "yes"
            farmerType = "existing"
            /**
             * Disable all editext and text
             */
            edtFarmerName.isEnabled = false
            edtContactsNo.isEnabled = false
            llFarmerName.isEnabled = false
            llState.isEnabled = false
            llDistrictName.isEnabled = false
            llTahsil.isEnabled = false
            llVillage.isEnabled = false
            llContactNo.isEnabled = false
            llProduct.isEnabled = true
            llCrop.isEnabled = true

            llFarmerName.background = resources.getDrawable(R.drawable.input_border_greyish)
            edtFarmerName.setTypeface(edtFarmerName.getTypeface(), Typeface.BOLD)
            llState.background = resources.getDrawable(R.drawable.input_border_greyish)
            txtState.setTypeface(edtFarmerName.getTypeface(), Typeface.BOLD)
            llDistrictName.background = resources.getDrawable(R.drawable.input_border_greyish)
            txtDistrictName.setTypeface(edtFarmerName.getTypeface(), Typeface.BOLD)
            llTahsil.background = resources.getDrawable(R.drawable.input_border_greyish)
            txtTahsil.setTypeface(edtFarmerName.getTypeface(), Typeface.BOLD)
            llVillage.background = resources.getDrawable(R.drawable.input_border_greyish)
            tvVillageName.setTypeface(edtFarmerName.getTypeface(), Typeface.BOLD)
            llContactNo.background = resources.getDrawable(R.drawable.input_border_greyish)
            edtContactsNo.setTypeface(edtFarmerName.getTypeface(), Typeface.BOLD)


            //get data from existing farmer
            getDataFromExistingFarmer()
        } else {
            isFromExisting = "no";
            farmerType = "new"
            imgIcPhotho.visibility = View.VISIBLE
            /**
             *enable all text and editext and set backgeound
             */

            edtFarmerName.isEnabled = true
            edtContactsNo.isEnabled = true

            llProduct.isEnabled = true
            llFarmerName.isEnabled = true
            llState.isEnabled = true
            llDistrictName.isEnabled = true
            llTahsil.isEnabled = true
            llVillage.isEnabled = true
            llContactNo.isEnabled = true


            llFarmerName.background = resources.getDrawable(R.drawable.input_border)
            edtFarmerName.setTypeface(edtFarmerName.getTypeface(), Typeface.NORMAL)
            llProduct.background = resources.getDrawable(R.drawable.input_border)
            txtProduct.setTypeface(edtFarmerName.getTypeface(), Typeface.NORMAL)
            llState.background = resources.getDrawable(R.drawable.input_border)
            txtState.setTypeface(edtFarmerName.getTypeface(), Typeface.NORMAL)
            llDistrictName.background = resources.getDrawable(R.drawable.input_border)
            txtDistrictName.setTypeface(edtFarmerName.getTypeface(), Typeface.NORMAL)
            llTahsil.background = resources.getDrawable(R.drawable.input_border)
            txtTahsil.setTypeface(edtFarmerName.getTypeface(), Typeface.NORMAL)
            llVillage.background = resources.getDrawable(R.drawable.input_border)
            tvVillageName.setTypeface(edtFarmerName.getTypeface(), Typeface.NORMAL)
            llContactNo.background = resources.getDrawable(R.drawable.input_border)
            edtContactsNo.setTypeface(edtFarmerName.getTypeface(), Typeface.NORMAL)


        }

        //add data in spray model
        addDataInSprayModel()

    }
    //get data from existing farmer

    private fun getDataFromExistingFarmer() {
        if (intent.hasExtra(Constants.FARMER_LIST)) {
            val existingData: ExistingData =
                intent.extras?.get(Constants.FARMER_LIST) as ExistingData
            setExistinData(existingData)
        }
    }

    //prefill existing farmer list
    private fun setExistinData(existingData: ExistingData) {
        edtFarmerName.setText(existingData.farmerName)
        edtFarmerName.setSelection(existingData.farmerName.length)

        txtState.text = existingData.state
        stateId = existingData.stateId
        txtTahsil.text = existingData.tehsil
        tahsilId = existingData.tehsilId
        getVillageList(tahsilId)
        txtDistrictName.text = existingData.district
        districtId = existingData.districtId
        tvVillageName.text = existingData.village
        villageId = existingData.village_id
        edtContactsNo.setText(existingData.contactNumber)
        txtCrop.text = existingData.crops[0].crop
        cropId = existingData.crops[0].crop_id
        getProductList(cropId)
        //if existing farmer, show prefill zero index value
        txtProduct.text = existingData.products[0].product
        productId = existingData.products[0].product_id

    }


    //add data in spray
    private fun addDataInSprayModel() {
        sprayList.add(SprayModel("Spray 1"))
        sprayList.add(SprayModel("Spray 2"))
    }

    //get crop productListData
    private fun getProductList(cropId: String) {
        showProgress()
        val model: String = preference.getString("token")
        val query = HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("crop_id", cropId)
        val gson = Gson()
        var j = gson.toJson(query)

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getpRoductResponse(query).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CropProductList>() {
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

                override fun onNext(listBean: CropProductList) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        cropProductList.clear()
                        cropProductList.addAll(listBean.data)

                    }
                }
            })

    }
    //end

    //get crop state list

    private fun getStateList() {
        var stateHashMap = HashMap<String, Any>()
        stateHashMap["apiToken"] = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .userStateNew(stateHashMap).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<StateResponse>() {
                override fun onCompleted() {
                }

                override fun onError(e: Throwable) {

                }

                override fun onNext(stateBean: StateResponse) {
                    if (!stateBean.data.isNullOrEmpty()) {
                        listState.addAll(stateBean.data)
                    }
                }
            })
    }
    //end

    //get crop tahsil list
    private fun getTahsilList(districtId: String) {
        val model: String = preference.getString("token")
        val query = HashMap<String, Any>()
        query["apiToken"] = model
        query["dId"] = districtId
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).userTehsilNew(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TahsilResponseNew>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                }

                override fun onNext(tehsilBean: TahsilResponseNew) {
                    if (!tehsilBean.data.isNullOrEmpty()) {
                        tahsilList.clear()
                        tahsilList.addAll(tehsilBean.data)
                    }
                }
            })
    }
    //end


    //get crop villageList
    private fun getVillageList(tehsilId: String) {
        showProgress()
        val model: String = preference.getString("token")
        val query = HashMap<String, Any>()
        // query.put("ECN", ecn)
        query["apiToken"] = model
        query["tehsil_id"] = tehsilId
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getAllVillageList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<AllVillageListResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(villageBean: AllVillageListResponse) {
                    hideProgress()
                    if (!villageBean.data.isNullOrEmpty()) {
                        villageList.clear()
                        villageList.addAll(villageBean.data)
                    }

                }
            }
            )
    }
    //end

    //get crop district api
    private fun getDistrictApi(sid: String) {
        showProgress()
        val districtHashMap = HashMap<String, Any>()
        districtHashMap["apiToken"] = preference.getString("token")
        districtHashMap["sId"] = sid
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .userDistrictNew(districtHashMap).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DistrictResponseNew>() {
                override fun onCompleted() {
                    hideProgress()

                }

                override fun onError(e: Throwable) {
                    hideProgress()

                }

                override fun onNext(districtBean: DistrictResponseNew) {
                    hideProgress()
                    distTrictList.addAll(districtBean.data)
                }
            })
    }
    //end


    //apply click on view
    private fun applyClickOnView() {
        llCrop.setOnClickListener(this)
        llProduct.setOnClickListener(this)
        llState.setOnClickListener(this)
        llDistrictName.setOnClickListener(this)
        llTahsil.setOnClickListener(this)
        llVillage.setOnClickListener(this)
        llShowDate.setOnClickListener(this)
        llSpray.setOnClickListener(this)
        imgCross.setOnClickListener(this)
        llSubmit.setOnClickListener(this)
        imgCreateDemo.setOnClickListener(this)
    }

    // apply click on view
    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.llCrop -> {
                cropSpinnerDialog = CropSpinnerDialogNew(
                    this,
                    crop1RecordResponseModel,
                    "Select or Search Crop",
                    object : OnGetId {
                        override fun onClick(item: String, position: Int, cropID: String) {
                            txtCrop.text = item
                            cropId = cropID
                            //get product list
                            productId = ""
                            txtProduct.text = ""
                            if (Connectivity.isConnected(this@CreateRequestActivity)) {
                                getProductList(cropId)
                            } else {
                                Single.fromCallable {
                                    cropProductList.clear()
                                    (appDatabase.productDao()
                                        .getAllProductList(cropId.toInt()) as ArrayList<ProductBean>).forEach {
                                        val data = CropProductData(it.product, it.product_id)
                                        cropProductList.add(data)
                                    }
                                }.subscribeOn(Schedulers.io())
                                    .observeOn(AndroidSchedulers.mainThread()).subscribe()
                            }

                        }
                    }
                )
                cropSpinnerDialog.showSpinerDialog()
            }
            R.id.llProduct -> {
                if (cropProductList.size > 0) {
                    cropProductSpinnerDialog = CropProductSpinnerDialog(
                        this,
                        cropProductList,
                        "Select or Search Product",
                        object : OnGetId {
                            override fun onClick(item: String, position: Int, productID: String) {
                                txtProduct.text = item
                                productId = productID
                            }

                        })
                    cropProductSpinnerDialog.showSpinerDialog()
                }
            }
            R.id.llState -> {
                if (listState.size > 0) {
                    stateSpinnerDialog = StateSpinnerDialogNew(
                        this,
                        listState,
                        "Select or Search State",
                        object : OnGetId {
                            override fun onClick(item: String, position: Int, sId: String) {
                                txtState.text = item
                                stateId = sId
                                if (sId.isNotEmpty()) {
                                    if (Connectivity.isConnected(this@CreateRequestActivity)) {
                                        getDistrictApi(sId)
                                    } else {
                                        Single.fromCallable {
                                            distTrictList.clear()
                                            val districtRecordResponseModel =
                                                appDatabase.districtDao()
                                                    .getAllDistrictList(sId.toInt()) as ArrayList<AllDistrictBean.DistrictData>
                                            districtRecordResponseModel.forEach {
                                                val data =
                                                    DistrictDataNew(it.district, it.district_id)
                                                distTrictList.add(data)
                                            }
                                        }.subscribeOn(Schedulers.io())
                                            .observeOn(AndroidSchedulers.mainThread()).subscribe()
                                    }
                                }
                            }

                        })
                    stateSpinnerDialog.showSpinerDialog()
                }
            }
            R.id.llDistrictName -> {
                if (distTrictList.size > 0) {
                    districtSpinnerDialog = DistrictSpinnerDialogNew(
                        this@CreateRequestActivity,
                        distTrictList,
                        "Select or Search District",
                        object : OnGetId {
                            override fun onClick(item: String, position: Int, sId: String) {
                                txtDistrictName.text = item
                                districtId = sId
                                if (Connectivity.isConnected(this@CreateRequestActivity)) {
                                    getTahsilList(sId)
                                } else {
                                    Single.fromCallable {
                                        tahsilList.clear()
                                        val tehsilRecordResponseModel = appDatabase.tehsilDao()
                                            .getAllTehsilList(sId.toInt()) as ArrayList<AllTehsilBean.TehsilData>
                                        tehsilRecordResponseModel.forEach {
                                            val data =
                                                TehsilDataNew(it.tehsil, it.tehsil_id)
                                            tahsilList.add(data)
                                        }
                                    }.subscribeOn(Schedulers.io())
                                        .observeOn(AndroidSchedulers.mainThread()).subscribe()
                                }

                            }

                        })
                    districtSpinnerDialog.showSpinerDialog()
                }
            }
            R.id.llTahsil -> {
                if (tahsilList.size > 0) {
                    tahsilSpinnerDialog = TahsilSpinnerDialogNew(
                        this@CreateRequestActivity,
                        tahsilList,
                        "Select or Search District",
                        object : OnGetId {
                            override fun onClick(item: String, position: Int, sId: String) {
                                txtTahsil.text = item
                                tahsilId = sId
                                if (Connectivity.isConnected(this@CreateRequestActivity)) {
                                    getVillageList(tahsilId)
                                } else {
                                    Single.fromCallable {
                                        villageList.clear()
                                        val village = appDatabase.villageDao()
                                            .getAllVillageList(tahsilId.toInt()) as ArrayList<AllVillageBean.VillageData>
                                        village.forEach {
                                            val data: AllVillageListResponse.Data =
                                                AllVillageListResponse.Data(
                                                    0,
                                                    ArrayList(),
                                                    it.district_id.toString(),
                                                    0,
                                                    "",
                                                    it.tehsil_id,
                                                    it.village_id,
                                                    it.village
                                                )
                                            villageList.add(data)
                                        }

                                    }.subscribeOn(Schedulers.io())
                                        .observeOn(AndroidSchedulers.mainThread()).subscribe()
                                }

                            }

                        })
                    tahsilSpinnerDialog.showSpinerDialog()
                }

            }
            R.id.llVillage -> {
                if (villageList.size > 0) {
                    villagelSpinnerDialog = AllVillageSpinnerDialog(
                        this@CreateRequestActivity,
                        villageList,
                        "Select or Search Village",
                        object : OnGetId {
                            override fun onClick(item: String, position: Int, sId: String) {
                                tvVillageName.text = item
                                villageId = sId
                            }

                        })
                    villagelSpinnerDialog.showSpinerDialog()
                }
            }
            R.id.llSpray -> {
                spraySpinnerDialog = SpraySpinnerDialogNew(
                    this@CreateRequestActivity,
                    sprayList,
                    "Select or Search Spray",
                    object : OnSpinerItemClick {
                        override fun onClick(item: String, position: Int) {
                            txtSpray.text = item
                        }

                    })
                spraySpinnerDialog.showSpinerDialog()
            }
            R.id.llShowDate -> {
                datePicker()
            }
            R.id.llSubmit -> {
                if (isValidateForm()) {
                    if (latitude == null || longtitude == null) {
                        getLocation()
                    } else
                        createDemoRequest()
                }

            }
            R.id.imgCreateDemo -> {
                if (file == null) {
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
                                    easyImage, this@CreateRequestActivity,
                                    { imageFile, _ ->
                                        imgIcPhotho.visibility = View.GONE
                                        file = ImageUtils.compressToExistingFile(
                                            imageFile.absolutePath,
                                            imageFile
                                        )
                                        if(file == null){
                                            file = imageFile
                                        }
                                        imgCross.visibility = View.VISIBLE
                                        imgCreateDemo.setImageBitmap(
                                            FileUtility.createBitmapFromFile(
                                                file
                                            )
                                        )
                                    },
                                    "",
                                )
                            }
                        }
                    }
                } else {
                    showdialogone()
                }
            }
            R.id.imgCross -> {
                file = null
                imgCreateDemo.invalidate();
                imgCreateDemo.setImageBitmap(null);
                imgIcPhotho.visibility = View.VISIBLE
                imgCross.visibility = View.GONE

            }

        }
    }
    //end


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

    private fun updateCropList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
        val prpdCropListNew = DatabaseManager.getProductCropDetailProductNew(this)
        prpdCropListNew?.forEach {
            val bean = CropBean()
            bean.setcID(it.cropId.toInt())
            bean.item = it.crop
            if (!checkValue(bean.getcID()!!))
                crop1RecordResponseModel.add(bean)
        }
    }

    //method for showing imgae in full screen
    private fun showdialogone() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_image)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(true)
        val msg = dialog.findViewById<View>(R.id.imgView) as ImageView
        var requestOptions = RequestOptions()
            .diskCacheStrategy(DiskCacheStrategy.NONE) // because file name is always same
            .skipMemoryCache(true)

        Glide.with(this)
            .load(file?.absoluteFile)
            .apply(requestOptions)
            .into(msg)
        dialog.show()
    }

    private fun checkValue(id: Int): Boolean {
        crop1RecordResponseModel.forEach {
            if (id == it.getcID()) {
                return true
            }
        }
        return false
    }

    //validate form
    fun isValidateForm(): Boolean {
        crop = txtCrop.text.trim().toString()
        productName = txtProduct.text.trim().toString()
        farmerName = edtFarmerName.text.trim().toString()
        state = txtState.text.trim().toString()
        districtName = txtDistrictName.text.trim().toString()
        pinCode = edtPinCode.text.trim().toString()
        tahsil = txtTahsil.text.trim().toString()
        village = tvVillageName.text.trim().toString()
        contactNo = edtContactsNo.text.trim().toString()
        showingDate = txtShowingDate.text.trim().toString()
        spray = txtSpray.text.trim().toString()

        if (TextUtils.isEmpty(crop)) {
            makeToast(resources.getString(R.string.txt_crop_source))
            return false
        } else if (TextUtils.isEmpty(productName)) {
            makeToast(resources.getString(R.string.txt_product_source))
            return false
        } else if (TextUtils.isEmpty(farmerName)) {
            makeToast(resources.getString(R.string.err_farmer_name))
            return false
        } else if (TextUtils.isEmpty(state)) {
            makeToast(resources.getString(R.string.txt_state_source))
            return false
        } else if (TextUtils.isEmpty(districtName)) {
            makeToast(resources.getString(R.string.txt_district_source))
            return false
        } else if (TextUtils.isEmpty(pinCode)) {
            makeToast(resources.getString(R.string.err_pinconde))
            return false
        } else if (TextUtils.isEmpty(tahsil)) {
            makeToast(resources.getString(R.string.txt_tehsil_source))
            return false
        } else if (TextUtils.isEmpty(village)) {
            makeToast(resources.getString(R.string.err_villages))
            return false
        } else if (TextUtils.isEmpty(contactNo)) {
            makeToast(resources.getString(R.string.err_contact_no))
            return false
        } else if (TextUtils.isEmpty(showingDate)) {
            makeToast(resources.getString(R.string.err_showing_date))
            return false
        } else if (TextUtils.isEmpty(spray)) {
            makeToast(resources.getString(R.string.err_spray))
            return false
        } else if (file == null) {
            if (farmerType.equals("new")) {
                makeToast(resources.getString(R.string.select_image))
                return false
            } else {
                return true
            }
        }

        return true
    }

    //show dialog
    fun datePicker(): Unit {
        val dialog = Dialog(this)
        dialog.window!!.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN)
        dialog.window!!.attributes.windowAnimations = R.style.PauseDialogAnimation
        dialog.setContentView(R.layout.dialog_date_picker)
        dialog.window!!.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.WRAP_CONTENT
        )

        dialog.window!!.setBackgroundDrawableResource(android.R.color.transparent)
        dialog.txtNa.setOnClickListener {
            txtShowingDate.text = "N/A"
            dialog.dismiss()
        }
        dialog.txtChooseDate.setOnClickListener {
            showDate()
            dialog.dismiss()
        }

        dialog.show()
    }


    /*
    show date
     */
    private fun showDate() {
        val c = Calendar.getInstance(Locale.ENGLISH)
        val year = c.get(Calendar.YEAR)
        val month = c.get(Calendar.MONTH)
        val day = c.get(Calendar.DAY_OF_MONTH)

        val mDatePicker = DatePickerDialog(
            activity,
            { view, year, monthOfYear, dayOfMonth ->

                // Display Selected date in textbox
                selectedDate = "" + dayOfMonth + "-" + (monthOfYear + 1) + "-" + year
                txtShowingDate.text = selectedDate

            },
            year,
            month,
            day
        )
        mDatePicker.datePicker.maxDate = System.currentTimeMillis();

        // Subtract 3 months from Calendar updated date
        c.add(Calendar.MONTH, -3)
        // Set the Calendar new date as minimum date of date picker
        mDatePicker.datePicker.minDate = c.timeInMillis;
        mDatePicker.show()
    }
    //end

    //create demo request

    private fun createDemoRequest() {
        llSubmit.isEnabled = false
        val model: String = preference.getString("token")
        val query = HashMap<String, RequestBody>()
        query.put("apiToken", getRequestBody(model))
        query.put("cropId", getRequestBody(cropId))
        query.put("productId", getRequestBody(productId))
        query.put("farmerName", getRequestBody(farmerName))
        query.put("stateId", getRequestBody(stateId))
        query.put("districtId", getRequestBody(districtId))
        query.put("tehsilId", getRequestBody(tahsilId))
        query.put("pincode", getRequestBody(pinCode))
        query.put("villageId", getRequestBody(villageId))
        query.put("contactNumber", getRequestBody(contactNo))
        query.put("showingDate", getRequestBody(showingDate))
        query.put("spray", getRequestBody(spray))
        query.put("latitude", getRequestBody(latitude.toString()))
        query.put("longitude", getRequestBody(longtitude.toString()))
        query.put("farmer_image", getRequestBody(imageUrl))
        query.put("farmer_type", getRequestBody(farmerType))


        val demoRequestOfflineModel = DemoRequestOfflineModel()
        demoRequestOfflineModel.apiToken = model
        demoRequestOfflineModel.cropId = cropId
        demoRequestOfflineModel.cropName = txtCrop.text.toString()
        demoRequestOfflineModel.productName = txtProduct.text.toString()
        demoRequestOfflineModel.productId = productId
        demoRequestOfflineModel.farmerName = farmerName
        demoRequestOfflineModel.stateId = stateId
        demoRequestOfflineModel.districtId = districtId
        demoRequestOfflineModel.tehsilId = tahsilId
        demoRequestOfflineModel.pincode = pinCode
        demoRequestOfflineModel.villageId = villageId
        demoRequestOfflineModel.contactNumber = contactNo
        demoRequestOfflineModel.showingDate = showingDate
        demoRequestOfflineModel.spray = spray
        demoRequestOfflineModel.latitude = latitude.toString()
        demoRequestOfflineModel.longitude = longtitude.toString()
        demoRequestOfflineModel.farmer_type = farmerType
        demoRequestOfflineModel.farmer_image = if (file != null) file!!.absolutePath else ""
        demoRequestOfflineModel.fileName = if (file != null) file!!.name else ""

        if (CheckConnectionStatus.isInternetAvailable(this)) {
            val okHttpClient = RetrofitClient.httpClient
            val mGson = GsonBuilder().excludeFieldsWithoutExposeAnnotation().create()
            val builder: Retrofit.Builder = Retrofit.Builder()
                .baseUrl(RetrofitClient.LIVE_URL)
                .client(okHttpClient)
                .addConverterFactory(GsonConverterFactory.create(mGson))
            val retrofit = builder.build()
            val client: ApiServiceRequest = retrofit.create(
                ApiServiceRequest::class.java)
            showProgress()
            if (file != null) {
                val fileBody1 = RequestBody.create(MediaType.parse("image/*"), file!!)
                val imagePart1: MultipartBody.Part =
                    MultipartBody.Part.createFormData("demoImage", file?.name, fileBody1)


                val call: Call<CreateRequestDemoResponse> =
                    client.createDemoRequestNew(query, imagePart1)
                call.enqueue(object : Callback<CreateRequestDemoResponse?> {
                    override fun onResponse(
                        call: Call<CreateRequestDemoResponse?>,
                        response: Response<CreateRequestDemoResponse?>
                    ) {
                        hideProgress()
                        if (call.isCanceled) {
                            insertDemoRequestInDb(demoRequestOfflineModel)
                            makeToast("Slow Internet Connection. Your Data will be automatically synced.")
                        } else
                            makeToast(response.message())
                        if (response.code() == 200)
                            finish()
                    }

                    override fun onFailure(call: Call<CreateRequestDemoResponse?>, t: Throwable) {
                        hideProgress()
                        if (call.isCanceled) {
                            insertDemoRequestInDb(demoRequestOfflineModel)
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
                val fileBody1 = RequestBody.create(MediaType.parse("image/*"), "")
                val imagePart1: MultipartBody.Part =
                    MultipartBody.Part.createFormData("demoImage", "", fileBody1)

                val call: Call<CreateRequestDemoResponse> =
                    client.createDemoRequestNew(query, imagePart1)
                call.enqueue(object : Callback<CreateRequestDemoResponse?> {
                    override fun onResponse(
                        call: Call<CreateRequestDemoResponse?>,
                        response: Response<CreateRequestDemoResponse?>
                    ) {
                        hideProgress()
                        if (call.isCanceled) {
                            insertDemoRequestInDb(demoRequestOfflineModel)
                            makeToast("Slow Internet Connection. Your Data will be automatically synced.")
                        } else {
                            makeToast(response.message())
                        }
                        if (response.code() == 200)
                            finish()
                    }

                    override fun onFailure(call: Call<CreateRequestDemoResponse?>, t: Throwable) {
                        hideProgress()
                        if (call.isCanceled) {
                            insertDemoRequestInDb(demoRequestOfflineModel)
                            makeToast("Slow Internet Connection. Your Data will be automatically synced.")
                            finish()
                        } else
                            makeToast("Failure")
                    }
                })
                Handler().postDelayed({
                    call.cancel()
                }, 5000)
            }
        } else {
            insertDemoRequestInDb(demoRequestOfflineModel)
            makeToast("No Internet Connection. Your data is saved Locally.")
            Handler().postDelayed({
                finish()
            }, 1000)

        }
    }

    private fun insertDemoRequestInDb(demoRequestOfflineModel: DemoRequestOfflineModel) {
        Single.fromCallable {
            val appDatabase = DatabaseClient.getInstance(this).appDatabase
            appDatabase.demoRequestDao().insert(demoRequestOfflineModel)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
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

    private fun getRequestBody(value: String): RequestBody {
        return RequestBody.create(MediaType.parse("application/json; charset=utf-8"), value)
    }

}
