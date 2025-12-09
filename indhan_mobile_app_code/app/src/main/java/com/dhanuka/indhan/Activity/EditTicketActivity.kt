package com.dhanuka.indhan.Activity

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.graphics.BitmapFactory
import android.location.Location
import android.location.LocationManager
import android.os.Bundle
import android.util.Log
import android.view.View
import android.view.WindowManager
import com.bumptech.glide.Glide
import com.dhanuka.indhan.Activity.EditTicketMVP.EditTicketContracts
import com.dhanuka.indhan.Activity.EditTicketMVP.EditTicketPresenterImpl
import com.dhanuka.indhan.Activity.EditTicketMVP.Model.EditTicketBean
import com.dhanuka.indhan.Activity.EditTicketMVP.SaveTicketMVP.SaveTicketContracts
import com.dhanuka.indhan.Activity.EditTicketMVP.SaveTicketMVP.SaveTicketPresenterImpl
import com.dhanuka.indhan.Activity.NewTicketMVP.*
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.DistrictContracts
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.DistrictPresenterImpl
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.DistrictSpinnerAdapter
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.DistrictSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictBean
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictModel
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.DataSourceBean
import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model.TehsilBean
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model.TehsilModel
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.TehsilContracts
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.TehsilPresenterImpl
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.TehsilSpinnerAdapter
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.TehsilSpinnerDialog
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ImageOfflineModel
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllStateBean
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllVillageBean
import com.dhanuka.indhan.Model.LanguageModel
import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.LanguageSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.*
import com.google.firebase.analytics.FirebaseAnalytics
import com.himangi.imagepreview.ImagePreviewActivity
import kotlinx.android.synthetic.main.activity_edit_ticket.*
import okhttp3.MediaType
import okhttp3.RequestBody
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


/**
 * Created by Shivam on 3/6/2018.
 */

class EditTicketActivity : BaseActivity(),
    EditTicketContracts.IEditTicketView,
    DistrictContracts.IDistrictView,
    TehsilContracts.ITehsilView,
    SaveTicketContracts.ISaveTicketView {

    override fun setVillageList(items: MyVillageBean) {
//        villageList = items.data as java.util.ArrayList<MyVillageBean.Data>
//        villageSpinnerDialog.updatelist(villageList)

    }

    override fun onFailure(items: String) {
        makeToast(items)
        logEventCustom(items, "v2/allVillageList")
    }

    private fun logEventCustom(mes: String?, apiName: String) {
        val bundle = HashMap<String, Any>()
        bundle[FirebaseAnalytics.Param.ITEM_ID] = preference.getString("user_id")
        bundle["Api_Name"] = apiName
        if (mes != null) {
            bundle["error"] = mes
        } else {
            bundle["error"] = "unknown error occurred"
        }
        bundle["user_detail"] = preference.getString("user_id")
            .plus("_${preference.getString("ecn")}")
            .plus("_${preference.getString("user_type")}")
            .plus(
                "_${preference.getString("Username")}"
                    .plus("_${preference.getString("designation")}")
            )
        Utility.logEvent("EditTicketActivity", "failed_api_$apiName", bundle)
    }

    private var ticket: TicketHistoryModel.Datum? = null
    private var easyImage: EasyImage? = null
    private lateinit var appDatabase: AppDatabase
    private var isUploaded: Boolean = false
    private lateinit var mNewTicketPresenter: EditTicketContracts.EditTicketPresenter
    private lateinit var msubmitTicketPresenter: SaveTicketContracts.SaveTicketPresenter
    private var Token = ""
    private var ImgUrl = ""
    private lateinit var dataSourceAdapter: DataSourceSpinnerAdapter
    private lateinit var districtAdapter: DistrictSpinnerAdapter
    private lateinit var tehsilAdapter: TehsilSpinnerAdapter

    private lateinit var dataSourceName: String
    private lateinit var stateName: String
    private lateinit var product1Name: String
    private lateinit var product2Name: String
    private lateinit var product3Name: String
    private lateinit var crop1Name: String
    private lateinit var crop2Name: String
    private lateinit var crop3Name: String
    private lateinit var districtName: String
    private lateinit var preDistrictName: String
    private lateinit var tehsilName: String
    private lateinit var preTehsilName: String

    private var dataSourceId: Int = 0
    private var stateId: Int = 0
    private var product1Id: Int = 0
    private var languageId: Int = 0
    private var product2Id: Int = 0
    private var product3Id: Int = 0
    private var crop1Id: Int = 0
    private var crop2Id: Int = 0
    private var crop3Id: Int = 0
    private var districtId: Int = 0
    private var preDistrictId: Int = 0
    private var tehsilId: Int = 0
    private var preTehsilId: Int = 0
    private var proCrop2 = true
    private var proCrop3 = true
    private var ticketId: Int = 0
    private var districtPosition: Int = 0
    private var tehsilPosition: Int = 0
    private var rowId = 0

    private lateinit var mdistrictPresenter: DistrictContracts.DistrictPresenter
    private lateinit var mtehsilPresenter: TehsilContracts.TehsilPresenter
    private lateinit var ticketNumber: String
    private lateinit var item: EditTicketBean
    private lateinit var preference: Preference
    private lateinit var languageSpinnerDialog: LanguageSpinner
    private lateinit var dataSourceSpinnerDialog: DataSourceSpinnerDialog
    private lateinit var stateSpinnerDialog: StateSpinnerNewDialog
    private lateinit var districtSpinnerDialog: DistrictSpinnerDialog
    private lateinit var tehsilSpinnerDialog: TehsilSpinnerDialog
    private lateinit var productSpinnerDialog: ProductSpinnerNewDialog
    private lateinit var product2SpinnerDialog: ProductSpinnerNewDialog
    private lateinit var product3SpinnerDialog: ProductSpinnerNewDialog
    private lateinit var cropSpinnerDialog: CropSpinnerNewDialog
    private lateinit var crop2SpinnerDialog: CropSpinnerNewDialog
    private lateinit var crop3SpinnerDialog: CropSpinnerNewDialog
    private lateinit var villageSpinnerDialog: VillageSpinnerNewDialog
    private var villageList: ArrayList<AllVillageBean.VillageData> = ArrayList()
    private var userType = ""
    private val dataSourceRecordResponseModel = ArrayList<DataSourceBean>()
    private val dataSourceBean = DataSourceBean()
    private val stateRecordResponseModel = ArrayList<AllStateBean.StateData>()
    private var languageList = ArrayList<LanguageModel>()
    private val districtRecordResponseModel = ArrayList<DistrictBean>()
    private val districtBean = DistrictBean()
    private val tehsilRecordResponseModel = ArrayList<TehsilBean>()
    private val tehsilBean = TehsilBean()
    private var product1RecordResponseModel =
        ArrayList<com.dhanuka.indhan.MenuScreenActivity.dbmodel.ProductBean>()
    private val crop1RecordResponseModel =
        ArrayList<com.dhanuka.indhan.MenuScreenActivity.dbmodel.CropBean>()
    private val cropBean = CropBean()
    var isAtp = "0"
    private var demoRequestId = "0"
    private var userFarmer = ""
    private var isTicketOffline = false
    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@EditTicketActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_edit_ticket)
        window.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN)
        val extras = intent.extras
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        initCamera()
        preference = Preference(this)
        ticketId = extras!!.getInt("ticketId")
        rowId = extras.getInt("rowId")
        ticketNumber = extras.getString("ticketNumber")!!
        ivSaveTicket.setOnClickListener(this)
        ivBackTick.setOnClickListener(this)
        tvReAddNew1.setOnClickListener(this)
        tvReAddNew2.setOnClickListener(this)
        tvReRemove1.setOnClickListener(this)
        tvReRemove2.setOnClickListener(this)
        spinnerDataSource.setOnClickListener(this)
        spinnerState.setOnClickListener(this)
        spinnerDistrict.setOnClickListener(this)
        spinnerTehsil.setOnClickListener(this)
        spinnerProduct1.setOnClickListener(this)
        spinnerProduct2.setOnClickListener(this)
        spinnerProduct3.setOnClickListener(this)
        spinnerCrop1.setOnClickListener(this)
        spinnerCrop2.setOnClickListener(this)
        spinnerCrop3.setOnClickListener(this)
        spinnerLanguage.setOnClickListener(this)
        img_src.setOnClickListener(this)
        img.setOnClickListener(this)

        spinnerVillage.setOnClickListener(this)
        tvReTicketNoDisp.text = ticketNumber
        languageList = DatabaseManager.getLanguageList(this)
        mNewTicketPresenter = EditTicketPresenterImpl(this)
        Token = Preference.getInstance(this).getString("token")
        languageSpinnerDialog = LanguageSpinner(this, languageList, "Select or Search Language")
        languageSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                languageId = languageList.get(position).id.toInt()
                spinnerLanguage.text = languageList.get(position).language
            }
        })
        dataSourceSpinnerDialog = DataSourceSpinnerDialog(
            this,
            dataSourceRecordResponseModel,
            "Select or Search Data Source"
        )
        stateSpinnerDialog =
            StateSpinnerNewDialog(this, stateRecordResponseModel, "Select or Search State")
        districtSpinnerDialog =
            DistrictSpinnerDialog(this, districtRecordResponseModel, "Select or Search District")
        tehsilSpinnerDialog =
            TehsilSpinnerDialog(this, tehsilRecordResponseModel, "Select or Search Tehsil")
        productSpinnerDialog =
            ProductSpinnerNewDialog(this, product1RecordResponseModel, "Select or Search Product")
        product2SpinnerDialog =
            ProductSpinnerNewDialog(this, product1RecordResponseModel, "Select or Search Product")
        product3SpinnerDialog =
            ProductSpinnerNewDialog(this, product1RecordResponseModel, "Select or Search Product")
        cropSpinnerDialog =
            CropSpinnerNewDialog(this, crop1RecordResponseModel, "Select or Search Crop")
        crop2SpinnerDialog =
            CropSpinnerNewDialog(this, crop1RecordResponseModel, "Select or Search Crop")
        crop3SpinnerDialog =
            CropSpinnerNewDialog(this, crop1RecordResponseModel, "Select or Search Crop")
        dataSourceAdapter = DataSourceSpinnerAdapter(this, 1)
        dataSourceAdapter.setData(dataSourceRecordResponseModel)
        districtAdapter = DistrictSpinnerAdapter(this, 1)
        districtAdapter.setData(districtRecordResponseModel)
        tehsilAdapter = TehsilSpinnerAdapter(this, 1)
        tehsilAdapter.setData(tehsilRecordResponseModel)

        getTicketDataFromDB()

        mdistrictPresenter = DistrictPresenterImpl(this)
        mtehsilPresenter = TehsilPresenterImpl(this)
        msubmitTicketPresenter = SaveTicketPresenterImpl(this)

        villageSpinnerDialog = VillageSpinnerNewDialog(this, villageList, "Select Village")
        villageSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerVillage.text = villageList[position].village
                villageId = villageList[position].village_id
            }
        })
    }

    private var villageId = ""
    private var mLocationListeners = arrayOf(
        LocationListener(LocationManager.GPS_PROVIDER),
        LocationListener(LocationManager.NETWORK_PROVIDER)
    )

    private var mLocationManager: LocationManager? = null
    private val LOCATION_INTERVAL: Long = 5000
    private val LOCATION_DISTANCE = 10f

    private fun initializeLocationManager() {
        if (mLocationManager == null) {
            mLocationManager =
                applicationContext.getSystemService(Context.LOCATION_SERVICE) as LocationManager
        }
    }

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

    var mLastLocation: Location? = null

    var isActiveLoader = false;

    var dbUpdateModel = ImageOfflineModel()


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
            appDatabase.ticketHistoryDao().isImageUploaded(rowId, true)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    override fun setItems(items: String) {
        if (!items.isEmpty()) {
            ImgUrl = items
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                Glide.with(this).load(ImgUrl).into(img_src)
                img_src.visibility = View.VISIBLE
            }
            dbUpdateModel.isUploaded = 1
            DatabaseManager.updateImageOfflineTable(this, dbUpdateModel)
            DatabaseManager.deleteAllImageOfflineTableParticular(this, ticketId)
        } else {
            img_src.visibility = View.GONE
        }
    }

    override fun setItems(items: SubmitTicketBean) {
        if (items.status_code == 200) {
            makeToast(items.message)
            launchIntent(TicketHistoryActivity::class.java, true)
        } else if (items.status_code == 401) {
            val preference: Preference = Preference.getInstance(this)
            preference.putString("ticketId", "")
            preference.putString("ticketTime", "")
            preference.putBolean("isLogin", false)
            preference.putString("token", "")
            launchIntent(LoginActivity::class.java, true)
        } else
            makeToast(items.message)
    }

    override fun setItems(items: TehsilModel) {
        if (items.status_code == 200) {
            spinnerTehsil.setText("Select Tehsil")
            tehsilId = 0
            tehsilRecordResponseModel.clear()
            tehsilRecordResponseModel.add(0, tehsilBean)
            tehsilBean.settID(0)
            tehsilBean.item = "Select Tehsil"
            tehsilRecordResponseModel.addAll(items.data!!)
            tehsilAdapter.notifyDataSetChanged()
            tehsilSpinnerDialog =
                TehsilSpinnerDialog(this, tehsilRecordResponseModel, "Select or Search Tehsil")
            tehsilSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    spinnerTehsil.text = item
                    tehsilId = tehsilRecordResponseModel[position].gettID()!!
                    tehsilName = tehsilRecordResponseModel[position].item.toString()
                }
            })
        } else if (items.status_code == 401) {
            var preference: Preference = Preference.getInstance(this)
            preference.putString("ticketId", "")
            preference.putString("ticketTime", "")
            preference.putBolean("isLogin", false)
            preference.putString("token", "")
            launchIntent(LoginActivity::class.java, true)

        } else
            makeToast(items.message)


        hideProgress()
    }

    override fun setItems(items: DistrictModel) {
        if (items.status_code == 200) {
            spinnerDistrict.text = "Select District"
            districtId = 0
            districtRecordResponseModel.clear()
            districtRecordResponseModel.add(0, districtBean)
            districtBean.setdID(0)
            districtBean.item = "Select District"
            districtRecordResponseModel.addAll(items.data!!)
            districtAdapter.notifyDataSetChanged()
            districtSpinnerDialog = DistrictSpinnerDialog(
                this,
                districtRecordResponseModel,
                "Select or Search District"
            )
            districtSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    spinnerDistrict.text = item
                    districtId = districtRecordResponseModel[position].getdID()!!
                    districtName = districtRecordResponseModel[position].item.toString()
                    if (districtId != 0) {
                        val query = HashMap<String, Any>()
                        query.put("apiToken", Token)
                        query.put("dId", districtId)
                        if (CheckConnectionStatus.isInternetAvailable(this@EditTicketActivity)) {
                            mtehsilPresenter.onSubmitClick(query)
                        }

                    }
                }
            })
            spinnerTehsil.text = "Select Tehsil"
            tehsilId = 0
            tehsilRecordResponseModel.clear()
            tehsilAdapter.notifyDataSetChanged()
        } else if (items.status_code == 401) {
            var preference: Preference = Preference.getInstance(this)
            preference.putString("ticketId", "")
            preference.putString("ticketTime", "")
            preference.putBolean("isLogin", false)
            preference.putString("token", "")
            launchIntent(LoginActivity::class.java, true)

        } else
            makeToast(items.message)
    }

    private fun getTicketDataFromDB() {
        Single.fromCallable {
            val item = appDatabase.ticketHistoryDao().getSingleTicketData(rowId)
            if (item != null)
                runOnUiThread {
                    setData(item)
                }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getDistrictAndTehsilId() {
        Single.fromCallable {
            val districtData = appDatabase.districtDao().getDistrictIdFromName(preDistrictName)
            preDistrictId = districtData.district_id.toInt()
            districtId = preDistrictId

            val tehsilData = appDatabase.tehsilDao().getTehsilIdFromData(preTehsilName)
            preTehsilId = tehsilData.tehsil_id.toInt()
            tehsilId = preTehsilId

            val dataSource = appDatabase.dataSourceDao().getDataSourceIdFromName(dataSourceName)
            dataSourceId = dataSource.dsID!!.toInt()

            val stateData = appDatabase.stateDao().getStateIdFromName(stateName)
            stateId = stateData.state_id.toInt()

            districtPosition = districtId
            tehsilPosition = tehsilId


            villageList = appDatabase.villageDao()
                .getAllVillageList(tehsilId) as ArrayList<AllVillageBean.VillageData>
            villageSpinnerDialog.updatelist(villageList)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun setData(item: TicketHistoryModel.Datum) {
        ticket = item
        if (item.uploadedFile != "")
            isUploaded = true
        spinnerVillage.setText(item.village)
        etRetExpFarmer.setText(item.expectedFarmers.toString())
        etRetExpPDA.setText(item.expectedPda.toString())
        etRetParName.setText(item.spName1)
        etRetParNum.setText(item.spNumber1)
        etRetParName2.setText(item.spName2)
        etRetParNum2.setText(item.spNumber2)
        etRetParName3.setText(item.spName3)
        etRetParNum3.setText(item.spNumber3)
        etRetParName4.setText(item.spName4)
        etRetParNum4.setText(item.spNumber4)
        etRetParName5.setText(item.spName5)
        isTicketOffline = item.isOffline
        etRetParNum5.setText(item.spNumber5)
        etRetParName6.setText(item.spName6)
        etRetParNum6.setText(item.spNumber6)
        demoRequestId = if (item.demo_request_id == null || item.demo_request_id.equals("null"))
            "0"
        else
            item.demo_request_id

        if (item.ticketStatus == "ACTIVE") {
            if (item.dataSource.equals("IFC", ignoreCase = true)) {
                img.visibility = View.GONE
            } else {
                img.visibility = View.VISIBLE
            }
        } else if (item.ticketStatus.equals("INACTIVE")) {
            img.visibility = View.GONE
        }

        userFarmer = item.user_farmer
        if (item.language_id != null && item.language != null) {
            languageId = item.language_id?.toInt() ?: 0
            spinnerLanguage.text = item.language
        }

        spinnerDataSource.text = item.dataSource
        dataSourceName = item.dataSource
        preDistrictName = item.district
        spinnerDistrict.text = preDistrictName
        spinnerTerritory.text = item.territory
        preTehsilName = item.tehsil
        spinnerTehsil.text = preTehsilName
        stateName = item.state
        spinnerState.text = item.state

        getDistrictAndTehsilId()
        dataSourceRecordResponseModel.clear()
        dataSourceRecordResponseModel.add(0, dataSourceBean)
        dataSourceBean.dsID = 0
        dataSourceBean.item = "Select Data Source"

        spinnerProduct1.text = item.product1
        spinnerCrop1.text = item.crop1

        if (!item.uploadedFile.isNullOrEmpty()) {
            ImgUrl = item.baseUrl + item.uploadedFile
            showImage()
        } else {
            checkOfflineUpload()
        }

//        isAtp = items.data?.ticket_data!![0].isATP!!
        if (dataSourceName.equals("Field Day", ignoreCase = true))
            tvReAddNew1.visibility = View.GONE
        else tvReAddNew1.visibility = View.VISIBLE
        if (isAtp == "1")
            tvReAddNew1.visibility = View.GONE
        else tvReAddNew1.visibility = View.VISIBLE

        getDataSourceListFromDB()
        getStateListFromDb()
        getCropAndProductId(item)
    }

    private fun getCropAndProductId(item: TicketHistoryModel.Datum) {
//        ticket = item
        Single.fromCallable {
            crop1Id = appDatabase.cropDao()
                .getCropIdFromName(spinnerCrop1.text.toString()).crop_id.toInt()
            val data = appDatabase.productDao().getAllProductList(crop1Id)
            for (i in data.indices)
                println("EditTicketActivity.getCropAndProductId : " + data[i].product)
            product1Id = appDatabase.productDao()
                .getProductIdFromName(crop1Id, spinnerProduct1.text.toString()).product_id.toInt()
            product1RecordResponseModel = appDatabase.productDao()
                .getAllProductList(crop1Id) as ArrayList<com.dhanuka.indhan.MenuScreenActivity.dbmodel.ProductBean>

            val secondProductId =
                appDatabase.productDao().getProductIdFromName(crop1Id, item.product2)
            val thirdProductId =
                appDatabase.productDao().getProductIdFromName(crop1Id, item.product3)
            crop1RecordResponseModel.clear()
            crop1RecordResponseModel.addAll(appDatabase.cropDao().allCropList)
            runOnUiThread {
                if (secondProductId != null && item.product2 != null && !secondProductId.product_id.equals(
                        "0",
                        ignoreCase = true
                    ) && item.crop2 != null && !crop1Id.toString().equals("0", ignoreCase = true)
                ) {
                    llView2.visibility = View.VISIBLE
                    product2Id = secondProductId.product_id.toInt()
                    crop2Id = crop1Id
                    spinnerProduct2.text = item.product2
                    spinnerCrop2.text = spinnerCrop1.text
                    tvReAddNew1.visibility = View.GONE
                }
                if (thirdProductId != null && item.product3 != null && !thirdProductId.product_id.equals(
                        "0",
                        ignoreCase = true
                    ) && item.crop3 != null && !crop1Id.toString().equals("0", ignoreCase = true)
                ) {
                    llView3.visibility = View.VISIBLE
                    product3Id = thirdProductId.product_id.toInt()
                    crop3Id = crop1Id
                    spinnerProduct3.text = item.product3
                    spinnerCrop3.text = spinnerCrop1.text
                    tvReAddNew2.visibility = View.GONE
                }
                setProductSpinner()
                setCropSpinner()
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun setProductSpinner() {
        productSpinnerDialog =
            ProductSpinnerNewDialog(this, product1RecordResponseModel, "Select or Search Product")
        productSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerProduct1.text = item
                product1Id = product1RecordResponseModel[position].product_id.toInt()
                product1Name = product1RecordResponseModel[position].product
                spinnerCrop1.text = "Select Crop"
                crop1Id = 0
                crop1Name = ""
            }
        })
        product2SpinnerDialog =
            ProductSpinnerNewDialog(this, product1RecordResponseModel, "Select or Search Product")
        product2SpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerProduct2.text = item
                product2Id = product1RecordResponseModel[position].product_id.toInt()
                product2Name = product1RecordResponseModel[position].product
                spinnerCrop2.text = "Select Crop"
                crop2Id = 0
                crop2Name = ""
            }
        })
        product3SpinnerDialog =
            ProductSpinnerNewDialog(this, product1RecordResponseModel, "Select or Search Product")
        product3SpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerProduct3.text = item
                product3Id = product1RecordResponseModel[position].product_id.toInt()
                product3Name = product1RecordResponseModel[position].product
                spinnerCrop3.text = "Select Crop"
                crop3Id = 0
                crop3Name = ""
            }
        })
    }

    private fun setCropSpinner() {
        cropSpinnerDialog =
            CropSpinnerNewDialog(this, crop1RecordResponseModel, "Select or Search Crop")
        cropSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerCrop1.text = item
                crop1Id = crop1RecordResponseModel[position].product_id.toInt()
                /*Recently Added By Aman*/
                crop3Id = crop1RecordResponseModel[position].product_id.toInt()
                crop2Id = crop1RecordResponseModel[position].product_id.toInt()
                crop1Name = crop1RecordResponseModel[position].crop
            }
        })

        crop2SpinnerDialog =
            CropSpinnerNewDialog(this, crop1RecordResponseModel, "Select or Search Crop")
        crop2SpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerCrop2.text = item
                crop3Id = crop1RecordResponseModel[position].product_id.toInt()
                crop2Name = crop1RecordResponseModel[position].crop
            }
        })

        crop3SpinnerDialog =
            CropSpinnerNewDialog(this, crop1RecordResponseModel, "Select or Search Crop")
        crop3SpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerCrop3.text = item
                crop3Id = crop1RecordResponseModel[position].product_id.toInt()
                crop3Name = crop1RecordResponseModel[position].crop
            }
        })
    }

    private fun getDataSourceListFromDB() {
        Single.fromCallable {
            dataSourceRecordResponseModel.addAll(appDatabase.dataSourceDao().allDataSourceList)
            dataSourceAdapter.notifyDataSetChanged()
            runOnUiThread {
                setDataSourceClick()
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun setDataSourceClick() {
        dataSourceSpinnerDialog = DataSourceSpinnerDialog(
            this,
            dataSourceRecordResponseModel,
            "Select or Search Data Source"
        )
        dataSourceSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerDataSource.text = item
                dataSourceId = dataSourceRecordResponseModel[position].dsID!!
                dataSourceName = dataSourceRecordResponseModel[position].item.toString()
                if (dataSourceName.equals("Field Day", ignoreCase = true)) {
                    tvReAddNew1.visibility = View.GONE
                    llView2.visibility = View.GONE
                    llView3.visibility = View.GONE
                    product2Id = 0
                    crop2Id = 0
                    product3Id = 0
                    crop3Id = 0
                    spinnerProduct2.text = "Select Product"
                    spinnerCrop2.text = "Select Crop"
                    spinnerProduct3.text = "Select Product"
                    spinnerCrop3.text = "Select Crop"
                } else {
                    tvReAddNew1.visibility = View.VISIBLE
                }
            }
        })
    }

    private fun getStateListFromDb() {
        Single.fromCallable {
            stateRecordResponseModel.clear()
            stateRecordResponseModel.addAll(appDatabase.stateDao().allStateList)
            stateSpinnerDialog =
                StateSpinnerNewDialog(this, stateRecordResponseModel, "Select or Search State")
            stateSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    spinnerState.text = item
                    stateId = stateRecordResponseModel[position].state_id.toInt()
                    stateName = stateRecordResponseModel[position].state
                    if (stateId != 0) {
                        val query = HashMap<String, Any>()
                        query.put("apiToken", Token)
                        query.put("sId", stateId)
                        if (CheckConnectionStatus.isInternetAvailable(this@EditTicketActivity)) {
                            mdistrictPresenter.onSubmitClick(query)
                        } else {
                            makeToast(resources.getString(R.string.no_internet_connection))
                        }
                    }
                }
            })
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    override fun setItems(items: EditTicketBean) {
        hideProgress()
        item = items
        if (items.status_code == 200) {
//            if (items.data?.ticket_data?.get(0)?.uploaded_file != null) {
//                isUploaded = true
//            }
//
//            val query1 = HashMap<String, Any>()
//            query1["tehsil_id"] = items.data?.ticket_data!![0].tehsil_id.toString()
//            query1["apiToken"] = Token
//
//            if (CheckConnectionStatus.isInternetAvailable(this@EditTicketActivity)) {
//                mNewTicketPresenter.onVillageList(query1)
//            } else {
//                makeToast(resources.getString(R.string.no_internet_connection))
//            }

//            spinnerVillage.setText(items.data?.ticket_data!![0].village)
//            etRetExpFarmer.setText(items.data?.ticket_data!![0].expected_farmers)
//            etRetExpPDA.setText(items.data?.ticket_data!![0].expected_pda)
//            etRetParName.setText(items.data?.ticket_data!![0].sp_name_1)
//            etRetParNum.setText(items.data?.ticket_data!![0].sp_number_1)
//            etRetParName2.setText(items.data?.ticket_data!![0].sp_name_2)
//            etRetParNum2.setText(items.data?.ticket_data!![0].sp_number_2)
//            etRetParName3.setText(items.data?.ticket_data!![0].sp_name_3)
//            etRetParNum3.setText(items.data?.ticket_data!![0].sp_number_3)
//            etRetParName4.setText(items.data?.ticket_data!![0].sp_name_4)
//            etRetParNum4.setText(items.data?.ticket_data!![0].sp_number_4)
//            etRetParName5.setText(items.data?.ticket_data!![0].sp_name_5)
//            etRetParNum5.setText(items.data?.ticket_data!![0].sp_number_5)
//            etRetParName6.setText(items.data?.ticket_data!![0].sp_name_6)
//            etRetParNum6.setText(items.data?.ticket_data!![0].sp_number_6)
//            if (items.data?.ticket_data!![0].language_id != null && items.data?.ticket_data!![0].language != null) {
//                languageId = items.data?.ticket_data!![0].language_id?.toInt() ?: 0
//                spinnerLanguage.text = items.data?.ticket_data!![0].language
//            }
//            preDistrictId = Integer.parseInt(items.data?.ticket_data!![0].district_id)
//            preDistrictName = items.data?.ticket_data!![0].district.toString()
//            preTehsilId = Integer.parseInt(items.data?.ticket_data!![0].tehsil_id)
//            preTehsilName = items.data?.ticket_data!![0].tehsil.toString()
//            spinnerDistrict.text = preDistrictName
//            districtId = preDistrictId
//            spinnerTehsil.text = preTehsilName
//            tehsilId = preTehsilId
//            dataSourceRecordResponseModel.clear()
//            dataSourceRecordResponseModel.add(0, dataSourceBean)
//            dataSourceBean.dsID = 0
//            dataSourceBean.item = "Select Data Source"

            /**
             * show image on the basis of data.!
             */
//            if (!items.data?.ticket_data!![0].uploaded_file.isNullOrEmpty()) {
//                ImgUrl = items.data?.ticket_data!![0].base_url + items.data?.ticket_data!![0].uploaded_file
//                showImage()
//            } else {
//                checkOfflineUpload()
//            }
//            dataSourceId = Integer.parseInt(items.data?.ticket_data!![0]?.data_source_id)
//            spinnerDataSource.text = items.data?.ticket_data!![0].data_source
//            dataSourceName = items.data?.ticket_data!![0].data_source.toString()
//
//            isAtp = items.data?.ticket_data!![0].isATP!!
//            if (dataSourceName.equals("Field Day", ignoreCase = true))
//                tvReAddNew1.visibility = View.GONE
//            else tvReAddNew1.visibility = View.VISIBLE
//            if (isAtp.equals("1"))
//                tvReAddNew1.visibility = View.GONE
//            else tvReAddNew1.visibility = View.VISIBLE


//            dataSourceRecordResponseModel.addAll(items.data?.data_source!!)
//            dataSourceAdapter.notifyDataSetChanged()
//            dataSourceSpinnerDialog = DataSourceSpinnerDialog(this, dataSourceRecordResponseModel, "Select or Search Data Source")
//            dataSourceSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
//                override fun onClick(item: String, position: Int) {
//                    spinnerDataSource.text = item
//                    dataSourceId = dataSourceRecordResponseModel[position].dsID!!
//                    dataSourceName = dataSourceRecordResponseModel[position].item.toString()
//                    if (dataSourceName.equals("Field Day", ignoreCase = true)) {
//                        tvReAddNew1.visibility = View.GONE
//                        llView2.visibility = View.GONE
//                        llView3.visibility = View.GONE
//                        product2Id = 0
//                        crop2Id = 0
//                        product3Id = 0
//                        crop3Id = 0
//                        spinnerProduct2.text = "Select Product"
//                        spinnerCrop2.text = "Select Crop"
//                        spinnerProduct3.text = "Select Product"
//                        spinnerCrop3.text = "Select Crop"
//                    } else {
//                        tvReAddNew1.visibility = View.VISIBLE
//                    }
//                }
//            })
//            stateRecordResponseModel.clear()
//            stateRecordResponseModel.add(0, stateBean)
//            stateBean.setsID(0)
//            stateBean.item = "Select State"
//            stateId = Integer.parseInt(items.data?.ticket_data!![0].state_id)
//            spinnerState.text = items.data?.ticket_data!![0].state
//            stateRecordResponseModel.addAll(items.data?.state_list!!)
//            stateAdapter.notifyDataSetChanged()
//            stateSpinnerDialog = StateSpinnerDialog(this, stateRecordResponseModel, "Select or Search State")
//            stateSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
//                override fun onClick(item: String, position: Int) {
//                    spinnerState.text = item
//                    stateId = stateRecordResponseModel[position].getsID()!!
//                    stateName = stateRecordResponseModel[position].item.toString()
//                    if (stateId != 0) {
//                        val query = HashMap<String, Any>()
//                        query.put("apiToken", Token)
//                        query.put("sId", stateId)
//                        if (CheckConnectionStatus.isInternetAvailable(this@EditTicketActivity)) {
//                            mdistrictPresenter.onSubmitClick(query)
//                        } else {
//                            makeToast(resources.getString(R.string.no_internet_connection))
//                        }
//                    }
//                }
//            })

            /* product1RecordResponseModel.clear()
             product1RecordResponseModel.addAll(items.data?.product_list!!)*/

//            product1Id = Integer.parseInt(items.data?.ticket_data!![0].product_1_id)
//            spinnerProduct1.text = items.data?.ticket_data!![0].product_1
//            product1Adapter.notifyDataSetChanged()
//            productSpinnerDialog = ProductSpinnerDialog(this, product1RecordResponseModel, "Select or Search Product")
//            productSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
//                override fun onClick(item: String, position: Int) {
//                    spinnerProduct1.text = item
//                    product1Id = product1RecordResponseModel[position].getpID()!!
//                    product1Name = product1RecordResponseModel[position].item.toString()
//                    spinnerCrop1.text = "Select Crop"
//                    crop1Id = 0
//                    crop1Name = ""
//                }
//            })
//            product2SpinnerDialog = ProductSpinnerDialog(this, product1RecordResponseModel, "Select or Search Product")
//            product2SpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
//                override fun onClick(item: String, position: Int) {
//                    spinnerProduct2.text = item
//                    product2Id = product1RecordResponseModel[position].getpID()!!
//                    product2Name = product1RecordResponseModel[position].item.toString()
//                    spinnerCrop2.text = "Select Crop"
//                    crop2Id = 0
//                    crop2Name = ""
//                }
//            })
//            product3SpinnerDialog = ProductSpinnerDialog(this, product1RecordResponseModel, "Select or Search Product")
//            product3SpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
//                override fun onClick(item: String, position: Int) {
//                    spinnerProduct3.text = item
//                    product3Id = product1RecordResponseModel[position].getpID()!!
//                    product3Name = product1RecordResponseModel[position].item.toString()
//                    spinnerCrop3.text = "Select Crop"
//                    crop3Id = 0
//                    crop3Name = ""
//                }
//            })
//            crop1RecordResponseModel.clear()
//            crop1RecordResponseModel.add(0, cropBean)
//            crop1RecordResponseModel.addAll(items.data?.crop_list!!)
//            cropBean.setcID(0)
//            cropBean.item = "Select Crop"
//
//            crop1Id = Integer.parseInt(items.data?.ticket_data!![0].crop_1_id)
//            items.data!!.product_list?.let { updateProductBeanList(crop1Id, it) }
//            spinnerCrop1.text = items.data?.ticket_data!![0].crop_1
//
//            crop1Adapter.notifyDataSetChanged()
//            cropSpinnerDialog = CropSpinnerDialog(this, crop1RecordResponseModel, "Select or Search Crop")
//            cropSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
//                override fun onClick(item: String, position: Int) {
//                    spinnerCrop1.text = item
//                    crop1Id = crop1RecordResponseModel[position].getcID()!!
//
//                    /*Recently Added By Aman*/
//                    crop3Id = crop1RecordResponseModel[position].getcID()!!
//                    crop2Id = crop1RecordResponseModel[position].getcID()!!
//                    crop1Name = crop1RecordResponseModel[position].item.toString()
//                }
//            })
//
//            crop2SpinnerDialog = CropSpinnerDialog(this, crop1RecordResponseModel, "Select or Search Crop")
//            crop2SpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
//                override fun onClick(item: String, position: Int) {
//                    spinnerCrop2.text = item
//                    crop3Id = crop1RecordResponseModel[position].getcID()!!
//                    crop2Name = crop1RecordResponseModel[position].item.toString()
//                }
//            })
//
//            crop3SpinnerDialog = CropSpinnerDialog(this, crop1RecordResponseModel, "Select or Search Crop")
//            crop3SpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
//                override fun onClick(item: String, position: Int) {
//                    spinnerCrop3.text = item
//                    crop3Id = crop1RecordResponseModel[position].getcID()!!
//                    crop3Name = crop1RecordResponseModel[position].item.toString()
//                }
//            })

//            districtPosition = Integer.parseInt(items.data?.ticket_data!![0].district_id)
//            tehsilPosition = Integer.parseInt(items.data?.ticket_data!![0].tehsil_id)
//            if (items.data?.ticket_data!![0].product_2 != null && !items.data?.ticket_data!![0].product_2_id.equals("0", ignoreCase = true) && items.data?.ticket_data!![0].crop_2 != null && !items.data?.ticket_data!![0].crop_2_id.equals("0", ignoreCase = true)) {
//                llView2.visibility = View.VISIBLE
//                product2Id = Integer.parseInt(items.data?.ticket_data!![0].product_2_id)
//                crop2Id = Integer.parseInt(items.data?.ticket_data!![0].crop_2_id)
//                spinnerProduct2.text = items.data?.ticket_data!![0].product_2
//                spinnerCrop2.text = items.data?.ticket_data!![0].crop_2
//                tvReAddNew1.visibility = View.GONE
//            }
//            if (items.data?.ticket_data!![0].product_3 != null && !items.data?.ticket_data!![0].product_3_id.equals("0", ignoreCase = true) && items.data?.ticket_data!![0].crop_3 != null && !items.data?.ticket_data!![0].crop_3_id.equals("0", ignoreCase = true)) {
//                llView3.visibility = View.VISIBLE
//                product3Id = Integer.parseInt(items.data?.ticket_data!![0].product_3_id)
//                crop3Id = Integer.parseInt(items.data?.ticket_data!![0].crop_3_id)
//                spinnerProduct3.text = items.data?.ticket_data!![0].product_3
//                spinnerCrop3.text = items.data?.ticket_data!![0].crop_3
//                tvReAddNew2.visibility = View.GONE
//            }
        } else if (items.status_code == 401) {
            makeToast("Permission is not granted for Editing.")
            val returnIntent = Intent()
            setResult(Activity.RESULT_CANCELED, returnIntent)
            finish()
        } else
            makeToast(items.message)
    }

    override fun onClick(v: View) {
        when (v.id) {
            R.id.img -> {
                userType = preference.getString("user_type")
                if (!userType.equals("dhanuka_doctor", false)) {
                    if (isUploaded) {
                        makeToast("Image has been already uploaded")
                    } else {
                        openPicture()
                    }
                } else {
                    if (ticket?.ddTicketRole.equals("1")) {
                        if (ticket?.uploadedByType == "" && !isUploaded) {
                            openPicture()
                        } else {
                            makeToast("Image has been already uploaded by ${ticket?.uploadedByType}")
                        }
                    } else {
                        makeToast("Only Fl can upload image")
                    }
                }
            }
            R.id.spinnerVillage -> {

                villageSpinnerDialog.showSpinerDialog()
            }
            R.id.img_src -> {
                showImageInFullScreen()
            }
            R.id.spinnerLanguage -> {
                if (!languageSpinnerDialog.isEnable)
                    languageSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerDataSource -> {
                if (isAtp.equals("0") && !dataSourceSpinnerDialog.isEnable)
                    dataSourceSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerState -> {
                if (isAtp.equals("0") && !stateSpinnerDialog.isEnable)
                    stateSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerDistrict -> {
                if (isAtp.equals("0") && !districtSpinnerDialog.isEnable)
                    districtSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerTehsil -> {
                if (isAtp.equals("0") && !tehsilSpinnerDialog.isEnable)
                    tehsilSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerProduct1 -> {
                if (isAtp.equals("0") && !productSpinnerDialog.isEnable)
                    productSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerProduct2 -> {
                if (!product2SpinnerDialog.isEnable)
                    product2SpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerProduct3 -> {
                if (!product3SpinnerDialog.isEnable)
                    product3SpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerCrop1 -> {
                if (isAtp.equals("0") && !cropSpinnerDialog.isEnable)
                    cropSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerCrop2 -> {
                if (!crop2SpinnerDialog.isEnable)
                    crop2SpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerCrop3 -> {
                if (!crop3SpinnerDialog.isEnable)
                    crop3SpinnerDialog.showSpinerDialog()
            }
            R.id.ivBackTick -> onBackPressed()
            R.id.tvReAddNew1 -> {
                llView2.visibility = View.VISIBLE
                tvReAddNew1.visibility = View.GONE
                tvReAddNew2.visibility = View.VISIBLE
                proCrop2 = true
                spinnerProduct2.text = "Select Product"
                spinnerCrop2.text = "Select Crop"
                crop2Id = 0
                product2Id = 0
            }
            R.id.tvReRemove1 -> {
                llView2.visibility = View.GONE
                tvReAddNew1.visibility = View.VISIBLE
                tvReAddNew2.visibility = View.GONE
                crop2Id = 0
                product2Id = 0
                proCrop2 = false
            }
            R.id.tvReAddNew2 -> {
                llView3.visibility = View.VISIBLE
                tvReAddNew2.visibility = View.GONE
                proCrop3 = true
                spinnerProduct3.text = "Select Product"
                spinnerCrop3.text = "Select Crop"
                crop3Id = 0
                product3Id = 0
            }
            R.id.tvReRemove2 -> {
                llView3.visibility = View.GONE
                tvReAddNew2.visibility = View.VISIBLE
                crop3Id = 0
                product3Id = 0
                proCrop3 = false
            }
            R.id.ivSaveTicket ->
                if (dataSourceId != 0) {
                    if (languageId != 0) {
                        if (stateId != 0) {
                            if (districtId != 0) {
                                if (tehsilId != 0) {
                                    if (product1Id != 0) {
                                        if (crop1Id != 0) {
                                            val query = HashMap<String, Any>()
                                            query.put("api_token", Token)
                                            query.put("ticId", ticketId)
                                            query.put("dsId", dataSourceId)
                                            query.put("sId", stateId)
                                            query.put("dId", districtId)
                                            query.put("tId", tehsilId)
                                            query.put("product_1", product1Id)
                                            query.put("crop_1", crop1Id)

                                            if (proCrop2 /*&& crop2Id != 0*/ && product2Id != 0) {
                                                query.put("product_2", product2Id)
                                                query.put("crop_2", crop2Id)
                                            } else {
                                                query.put("product_2", "")
                                                query.put("crop_2", "")
                                            }
                                            if (proCrop3 /* && crop3Id != 0*/ && product3Id != 0) {
                                                query.put("product_3", product3Id)
                                                query.put("crop_3", crop3Id)
                                            } else {
                                                query.put("product_3", "")
                                                query.put("crop_3", "")
                                            }
                                            query.put(
                                                "village",
                                                spinnerVillage.text.toString().trim()
                                            )
                                            query.put("language_id", languageId)
                                            query.put(
                                                "expected_farmers",
                                                etRetExpFarmer.text.toString().trim()
                                            )
                                            query.put(
                                                "expected_pda",
                                                etRetExpPDA.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_name_1",
                                                etRetParName.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_number_1",
                                                etRetParNum.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_name_2",
                                                etRetParName2.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_number_2",
                                                etRetParNum2.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_name_3",
                                                etRetParName3.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_number_3",
                                                etRetParNum3.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_name_4",
                                                etRetParName4.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_number_4",
                                                etRetParNum4.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_name_5",
                                                etRetParName5.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_number_5",
                                                etRetParNum5.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_name_6",
                                                etRetParName6.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_number_6",
                                                etRetParNum6.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_number_6",
                                                etRetParNum6.text.toString().trim()
                                            )
                                            query.put("demo_request_id", demoRequestId)
                                            query.put("user_farmer", userFarmer)

                                            if (CheckConnectionStatus.isInternetAvailable(this)) {
                                                msubmitTicketPresenter.onSubmitClick(query)
                                            } else {
                                                updateTicket()
                                            }
                                        } else {
                                            makeToast(resources.getString(R.string.txt_crop_source))
                                        }
                                    } else {
                                        makeToast(resources.getString(R.string.txt_product_source))
                                    }
                                } else {
                                    makeToast(resources.getString(R.string.txt_tehsil_source))
                                }
                            } else {
                                makeToast(resources.getString(R.string.txt_district_source))
                            }
                        } else {
                            makeToast(resources.getString(R.string.txt_state_source))
                        }
                    } else {
                        makeToast(resources.getString(R.string.txt_language))
                    }
                } else {
                    makeToast(resources.getString(R.string.txt_data_source))
                }
            else -> {
            }
        }

    }

    private fun updateTicket() {
        Single.fromCallable {
            val query = HashMap<String, Any>()
            appDatabase.ticketHistoryDao().updateDataSourceId(rowId, dataSourceId.toString())
            appDatabase.ticketHistoryDao().updateStateId(rowId, stateId.toString())
            appDatabase.ticketHistoryDao().updateDistrictId(rowId, districtId.toString())
            appDatabase.ticketHistoryDao().updateTehsilId(rowId, tehsilId.toString())
            appDatabase.ticketHistoryDao().updateCrop1Id(rowId, crop1Id.toString())
            appDatabase.ticketHistoryDao().updateProduct1Id(rowId, product1Id.toString())
            if (proCrop2 && product2Id != 0) {
                appDatabase.ticketHistoryDao().updateCrop2Id(rowId, crop2Id.toString())
                appDatabase.ticketHistoryDao().updateProduct2Id(rowId, product2Id.toString())
            } else {
                appDatabase.ticketHistoryDao().updateCrop2Id(rowId, "")
                appDatabase.ticketHistoryDao().updateProduct2Id(rowId, "")
                appDatabase.ticketHistoryDao().updateProduct2Name(rowId, "")
                appDatabase.ticketHistoryDao().updateCrop2Name(rowId, "")
            }
            if (proCrop3 && product3Id != 0) {
                appDatabase.ticketHistoryDao().updateCrop3Id(rowId, crop3Id.toString())
                appDatabase.ticketHistoryDao().updateProduct3Id(rowId, product3Id.toString())
            } else {
                appDatabase.ticketHistoryDao().updateCrop3Id(rowId, "")
                appDatabase.ticketHistoryDao().updateProduct3Id(rowId, "")
                appDatabase.ticketHistoryDao().updateProduct3Name(rowId, "")
                appDatabase.ticketHistoryDao().updateCrop3Name(rowId, "")
            }
            appDatabase.ticketHistoryDao()
                .updateVillageName(rowId, spinnerVillage.text.toString().trim())
            appDatabase.ticketHistoryDao().updateVillageId(rowId, villageId)
            appDatabase.ticketHistoryDao().updateLanguageId(rowId, languageId.toString())
            appDatabase.ticketHistoryDao()
                .updateExpectedFarmer(rowId, etRetExpFarmer.text.toString().trim())
            appDatabase.ticketHistoryDao()
                .updateExpectedPda(rowId, etRetExpPDA.text.toString().trim())
            appDatabase.ticketHistoryDao().updateName1(rowId, etRetParName.text.toString().trim())
            appDatabase.ticketHistoryDao().updateNumber1(rowId, etRetParNum.text.toString().trim())

            appDatabase.ticketHistoryDao().updateName2(rowId, etRetParName2.text.toString().trim())
            appDatabase.ticketHistoryDao().updateNumber2(rowId, etRetParNum2.text.toString().trim())

            appDatabase.ticketHistoryDao().updateName3(rowId, etRetParName3.text.toString().trim())
            appDatabase.ticketHistoryDao().updateNumber3(rowId, etRetParNum3.text.toString().trim())

            appDatabase.ticketHistoryDao().updateName4(rowId, etRetParName4.text.toString().trim())
            appDatabase.ticketHistoryDao().updateNumber4(rowId, etRetParNum4.text.toString().trim())

            appDatabase.ticketHistoryDao().updateName5(rowId, etRetParName5.text.toString().trim())
            appDatabase.ticketHistoryDao().updateNumber5(rowId, etRetParNum5.text.toString().trim())

            appDatabase.ticketHistoryDao().updateName6(rowId, etRetParName6.text.toString().trim())
            appDatabase.ticketHistoryDao().updateNumber6(rowId, etRetParNum6.text.toString().trim())

            appDatabase.ticketHistoryDao().updateDemoRequestId(rowId, demoRequestId)
            appDatabase.ticketHistoryDao().updateUserFarmer(rowId, userFarmer)
            appDatabase.ticketHistoryDao().isTicketUpdated(rowId, true)
            runOnUiThread {
                makeToast("No Internet Connection. Data will be synced when online.")
                launchIntent(TicketHistoryActivity::class.java, true)
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    override fun hideProgress() {
        dismissProgress()
    }

    private fun showImage() {
        Glide.with(this).load(ImgUrl).into(img_src)
        img_src.visibility = View.VISIBLE
    }

    private fun showImageInFullScreen() {
        var intent = Intent(this, ImagePreviewActivity::class.java)
        if (ImgUrl.length > 5) {
            val imageList: ArrayList<String> = ArrayList()
            imageList.add(ImgUrl)
            intent.putExtra(ImagePreviewActivity.IMAGE_LIST, imageList);
            intent.putExtra(ImagePreviewActivity.CURRENT_ITEM, 0);
            startActivity(intent)
        }
    }

    private fun openPicture() {
        ImagePickerUtil.pickFromCamera2(easyImage, this@EditTicketActivity, { imageFile, _ ->
            var file = ImageUtils.compressToExistingFile(
                imageFile.absolutePath,
                imageFile
            )

            if(file == null){
                file = imageFile
            }
            val bitmap = BitmapFactory.decodeFile(file.path.toString())
            img_src.visibility = View.VISIBLE
            // img_src.setImageBitmap(bitmap)
            if (bitmap != null) {
                img_src.setImageBitmap(bitmap)
                dbUpdateModel.image_url_local = file.path.toString()
                dbUpdateModel.ticket_id = ticketId.toString()
                dbUpdateModel.isUploaded = 0
                dbUpdateModel.type = Constants.IS_TICKET
                val cal = Calendar.getInstance(Locale.ENGLISH)
                val format1 = SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
                val formatted = format1.format(cal.time)
                println(formatted)
                dbUpdateModel.date = formatted
                getLocation()
                isActiveLoader = true
                if (CheckConnectionStatus.isInternetAvailable(this@EditTicketActivity)) {
                    val imgfile = File(file.path.toString())
                    val convert_File_2String = imgfile.toString()
                    ImgUrl = imgfile.toString()
                    val fileNAme =
                        convert_File_2String.substring(convert_File_2String.lastIndexOf("/") + 1)
                    val reqFile = RequestBody.create(MediaType.parse("image/*"), imgfile)
                    val ticke =
                        RequestBody.create(MediaType.parse("text/plain"), ticketId.toString())
                    val token_req = RequestBody.create(MediaType.parse("text/plain"), Token)
                    val lat = RequestBody.create(
                        MediaType.parse("text/plain"),
                        if (dbUpdateModel.lattitude != null) dbUpdateModel.lattitude else preference.getString(
                            Constants.latitude
                        )
                    )
                    val long = RequestBody.create(
                        MediaType.parse("text/plain"),
                        if (dbUpdateModel.longitude != null) dbUpdateModel.longitude else preference.getString(
                            Constants.longitude
                        )
                    )
                    val date = RequestBody.create(MediaType.parse("text/plain"), dbUpdateModel.date)
                    mNewTicketPresenter.onSubmitPhoto(
                        reqFile,
                        ticke,
                        token_req,
                        fileNAme,
                        lat,
                        long,
                        date
                    )
                } else {
                    dbUpdateModel.lattitude =
                        if (dbUpdateModel.lattitude != null) mLastLocation?.longitude.toString() else preference.getString(
                            Constants.latitude
                        )
                    dbUpdateModel.longitude =
                        if (dbUpdateModel.lattitude != null) mLastLocation?.longitude.toString() else preference.getString(
                            Constants.latitude
                        )
                    DatabaseManager.saveImageOfflineTable(
                        this,
                        dbUpdateModel,
                        if (isTicketOffline) 1 else 0
                    )

                    if (isTicketOffline)
                        updateImageUploadedInDB()
                }
            }
        }, "");
//        .
//        val takePictureIntent = Intent(this, CameraActivity::class.java)
//        startActivityForResult(takePictureIntent, 101)
    }

    private fun checkOfflineUpload() {
        var imageModel = DatabaseManager.getNonUploadedImageOfflineTable(this, ticketId.toString())
        if (imageModel != null) {
            var imgfile = File(imageModel.image_url_local)
            var bitmap = BitmapFactory.decodeFile(imgfile.toString())
            //   var bitmap = BitmapFactory.decodeFile(dataString.toString())
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

//    private fun updateProductBeanList(id: Int, productList: java.util.ArrayList<ProductBean>) {
//        productList.forEach {
//            if (it.crop_id.equals(id.toString())) {
//                product1RecordResponseModel.add(it)
//            }
//        }
//    }
}
