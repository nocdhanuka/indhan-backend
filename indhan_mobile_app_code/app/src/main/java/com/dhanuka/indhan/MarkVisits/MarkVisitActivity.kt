package com.dhanuka.indhan.MarkVisits

import android.app.Dialog
import android.content.Intent
import android.graphics.BitmapFactory
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.view.View
import android.view.Window
import android.widget.ImageView
import com.bumptech.glide.Glide
import com.bumptech.glide.load.engine.DiskCacheStrategy
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.CropSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.FarmerSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Activity.NewTicketMVP.ProductSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.VillageSpinnerDialog
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.farmerlist.FarmerBean
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.*
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_mark_visit.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.joda.time.DateTime
import pl.aprilapps.easyphotopicker.DefaultCallback
import pl.aprilapps.easyphotopicker.EasyImage
import pl.aprilapps.easyphotopicker.MediaFile
import pl.aprilapps.easyphotopicker.MediaSource
import rx.Single
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.io.File


class MarkVisitActivity : BaseActivity(), MarkVisitContractor.MarkVisitView,
    SelectDateFragment.myDialogListener {

    private var easyImage: EasyImage? = null
    private lateinit var databaseClient: AppDatabase
    private lateinit var productSpinner: ProductSpinnerDialog
    private lateinit var villageSpinnerDialog: VillageSpinnerDialog
    private lateinit var cropSpinner: CropSpinnerDialog
    private lateinit var farmerSpinner: FarmerSpinnerDialog

    private var productList: ArrayList<ProductBean> = ArrayList()
    private var cropList: ArrayList<CropBean> = ArrayList()
    private var villageList: ArrayList<MyVillageBean.MyVillageData> = ArrayList()
    private var farmerList: ArrayList<FarmerBean.VillageFarmerData> = ArrayList()
    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList()

    var product = ""
    var product_id = 0
    var crop_id = 0
    var cropName = ""
    var villageId = ""
    var farmerId = ""

    private var file: File? = null
    private lateinit var preference: Preference
    private lateinit var mPresenter: MarkVisitContractor.MarkVisitPresenter


    override fun setVillageList(items: FarmerBean) {
        farmerList = items.data as ArrayList<FarmerBean.VillageFarmerData>
        farmerSpinner.updatelist(farmerList)
    }

    override fun setVillageList(items: MyVillageBean) {
        villageList = items.data as ArrayList<MyVillageBean.MyVillageData>
        villageSpinnerDialog.updatelist(villageList)
    }

    override fun onStartDateSelected(startDate: String, value: Int) {
        txtDateName.text = startDate
    }

    override fun onEndDateSelected(startDate: String, value: Int) {
    }

    override fun onClick(v: View?) {
    }

    override fun setItems(items: MarkVisitBean) {
        makeToast(items.message)
        finish()
    }

    override fun failure(items: String) {
        makeToast(items)
    }


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_mark_visit)
        databaseClient = DatabaseClient.getInstance(this).appDatabase
        mPresenter = MarkVisitPresenter(this)
        preference = Preference(this)
        initCamera()
        val query = HashMap<String, Any>()
        query["ECN"] = preference.getString("ecn")
        query["apiToken"] = preference.getString("token")

        ivOfflineData.setOnClickListener {
            launchIntent(MarkVisitOfflineActivity::class.java, false)
        }

        /*
        * get crop list from DB
        * */
        updateCropList()

        /*
        * spinner
        * */
        cropSpinner = CropSpinnerDialog(this, cropList, "Select Crop")
        cropSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                txtCropName.text = item
                crop_id = cropList.get(position).getcID()!!
                productList.clear()
                updateProductList(crop_id)
            }
        })

        villageSpinnerDialog = VillageSpinnerDialog(this, villageList, "Select Village")
        villageSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {

            override fun onClick(item: String, position: Int) {
                txtVillageName.text = villageList[position].villageName
                villageId = villageList[position].villageId
                txtCropName.hint = "Select Crop Name"
                txtFarmersName.hint = "Select Farmers Name"
                txtCropName.text = ""
                txtFarmersName.text = ""
                crop_id = 0
                farmerId = ""
                getFarmerDataOffline(villageId)
            }
        })

        getVillageDataOffline()
        farmerSpinner = FarmerSpinnerDialog(this, farmerList, "Select Farmer")
        farmerSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                farmerId = farmerList[position].farmerId
                txtFarmersName.text = farmerList[position].farmerName
                txtCropName.text = ""
                txtCropName.hint = "Select Crop Name"
                crop_id = 0
            }
        })

        productSpinner = ProductSpinnerDialog(this, productList, "Select Product")
        productSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                txtProductName.text = item
                product_id = productList[position].getpID()!!
            }
        })

        txtCropName.setOnClickListener {
            if (!cropSpinner.isEnable)
                cropSpinner.showSpinerDialog()
        }

        txtProductName.setOnClickListener {
            if (!productSpinner.isEnable)
                productSpinner.showSpinerDialog()
        }

        txtVillageName.setOnClickListener {
            if (!villageSpinnerDialog.isEnable)
                villageSpinnerDialog.showSpinerDialog()
        }

        txtDateName.setOnClickListener {
            Utility.getPickedFutureDate(this, object : Utility.DatePickCallBack {
                override fun onCancel() {}

                override fun onDateSet(date: String?, formattedDate: String?) {
                    txtDateName.text = date
                }
            })
        }

        rlPicture.setOnClickListener {
            if (file == null) {
                PermissionsUtil.askPermissions(
                    this@MarkVisitActivity,
                    PermissionsUtil.CAMERA,
                    PermissionsUtil.STORAGE
                ) {
                    if (it) {
                        ImagePickerUtil.pickFromCamera2(
                            easyImage,
                            this@MarkVisitActivity,
                            { imagefile, _ ->
                                file = ImageUtils.compressToExistingFile(
                                    imagefile.absolutePath,
                                    imagefile
                                )
                                if(file == null){
                                    file = imagefile
                                }
                                val bitmap = BitmapFactory.decodeFile(file?.absolutePath)
                                imgPicture.setImageBitmap(bitmap)
                                imgCross.visibility = View.VISIBLE
                                imgCamera.visibility = View.GONE
                            },
                            ""
                        )
                    }
                }
            } else {
                showdialogone()
            }
        }

        imgCross.setOnClickListener {
            file = null
            imgCross.visibility = View.GONE
            imgCamera.visibility = View.VISIBLE
            imgPicture.setImageDrawable(null)
        }

        txtFarmersName.setOnClickListener {
            if (!farmerSpinner.isEnable)
                farmerSpinner.showSpinerDialog()
        }

        btnNext.setOnClickListener {
            if (validationList()) {
                showProgress()
                val model = MarkVisitRequestModel(
                    cropId = crop_id.toString(),
                    farmerId = farmerId,
                    imgFile = file!!.path,
                    imgName = file!!.name,
                    villageId = villageId,
                    id = System.currentTimeMillis().toInt(),
                    cropName = txtCropName.text.toString().trim(),
                    farmerName = txtFarmersName.text.toString().trim(),
                    villageName = txtVillageName.text.toString().trim()
                )
                saveMarkVisitDataLocally(model)
            }
        }
    }

    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@MarkVisitActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
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

    private fun saveMarkVisitDataLocally(model: MarkVisitRequestModel) {
        GlobalScope.launch() {
            var size = 0;
            withContext(Dispatchers.IO) {
                val appDatabase = DatabaseClient.getInstance(this@MarkVisitActivity).appDatabase
                appDatabase.markVisitRequestDao().insert(model)
                size = appDatabase.markVisitRequestDao().getAllMarkVisitData()?.size ?: 0
            }.run {
                withContext(Dispatchers.Main) {
                    makeToast("Mark visit saved successfully.")
                    val logModel = LogModel()
                    logModel.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
                    val data = "Mark visit data : ${Gson().toJson(model)} | Mark visit in local db size : $size"
                    logModel.apiData = data
                    logModel.apiName = "Save mark visit data in local db"
                    val userData = (preference.getString("user_id") + "_"
                            + preference.getString("ecn") + "_"
                            + preference.getString("user_type") + "_"
                            + preference.getString("Username") + "_"
                            + preference.getString("designation"))
                    logModel.userData = "\nUSER DATA : -> $userData"
                    AppLogFile.writeLineInFile(Gson().toJson(logModel), preference.getString("ecn"))
                    hideProgress()
                    finish()
                }
            }
        }
    }

    private fun getFarmerDataOffline(villageId: String) {
        Single.fromCallable {
            farmerList.clear()
            databaseClient.allFarmerListDao().allFarmerList?.size
            val farmerData = databaseClient.allFarmerListDao().getVillageAllFarmerList(villageId)
            farmerData?.let {
                farmerList.addAll(it as ArrayList<FarmerBean.VillageFarmerData>)
            }
            farmerSpinner.updatelist(farmerList)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).subscribe()
    }

    private fun getVillageDataOffline() {
        villageList.clear()
        Single.fromCallable {
            val list = databaseClient.myVillageDao().myVillageList
            villageList.addAll(list)
            villageSpinnerDialog.updatelist(villageList)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).subscribe()
    }

    private fun updateProductList(crop_id: Int) {

        prodCropResponseModel.forEach {
            if (it.cropId.equals(crop_id.toString())) {
                val crop = ProductBean()
                crop.setpID(it.productId.toInt())
                crop.item = it.product
                productList.add(crop)
            }
        }
        productSpinner.updatelist(productList)

    }

    private fun updateCropList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
        DatabaseManager.getProductCropDetailProductNew(this)?.forEach {
            val bean = CropBean()
            bean.setcID(it.cropId.toInt())
            bean.item = it.crop
            if (!checkValue(bean.getcID()!!))
                cropList.add(bean)
        }
    }

    private fun checkValue(id: Int): Boolean {
        productList.forEach {
            if (id == it.getpID()) {
                return true
            }
        }
        return false
    }

    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<out String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        PermissionsUtil.onRequestPermissionsResult(requestCode, permissions, grantResults)
    }

    private fun validationList(): Boolean {
        when {
            villageId == "" -> {
                makeToast("Please Select Village Name")
                return false
            }
            farmerId == "" -> {
                makeToast("Please Enter Farmer Name")
                return false
            }
            crop_id == 0 -> {
                makeToast("Please Select Crop Name")
                return false
            }
            file == null -> {
                makeToast("Please Click a picture ")
                return false
            }
            else -> return true
        }

    }

    private fun showdialogone() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_image)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(true)
        val msg = dialog.findViewById<View>(R.id.imgView) as ImageView
        val requestOptions = RequestOptions()
            .diskCacheStrategy(DiskCacheStrategy.NONE) // because file name is always same
            .skipMemoryCache(true)

        Glide.with(this)
            .load(file?.absoluteFile)
            .apply(requestOptions)
            .into(msg)
        dialog.show()
    }
}
