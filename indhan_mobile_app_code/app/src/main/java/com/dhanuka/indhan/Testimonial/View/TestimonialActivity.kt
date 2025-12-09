package com.dhanuka.indhan.PlanATP.View

import android.app.ActivityManager
import android.app.Dialog
import android.app.ProgressDialog
import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.media.ThumbnailUtils
import android.os.Build
import android.os.Bundle
import android.provider.MediaStore
import android.text.Editable
import android.text.TextWatcher
import android.view.View
import android.view.Window
import android.view.WindowManager
import androidx.annotation.RequiresApi
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.CropSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Activity.NewTicketMVP.ProductSpinnerDialog
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.ActivateTicketAdapter
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Testimonial.Testimonial_MVP.TestimonialFactory
import com.dhanuka.indhan.Testimonial.Testimonial_MVP.TestimonialPresenterImpl
import com.dhanuka.indhan.Testimonial.service.ServiceConstrants
import com.dhanuka.indhan.Testimonial.service.TestimonialService
import com.dhanuka.indhan.util.*
import com.himangi.imagepreview.ImagePreviewActivity
import kotlinx.android.synthetic.main.activity_testimonial.*
import java.io.File

/**
 * Created by John on 5/26/2018.
 */
class TestimonialActivity : BaseActivity(), TestimonialFactory.TestimonialHomeListener {

    override fun onFailure(mes: String) {
        isUpdating = false
        if (!mes.equals("Failure"))
            makeToast(mes)
    }

    override fun onSuccess(model: String) {
        showdialog()
    }

    lateinit var preference: Preference
    lateinit var token: String
    var testimonial_str = ""
    lateinit var adapter: ActivateTicketAdapter

    // lateinit var model: TicketList
    var ImgUrl = ""
    var url = ""

    // var isActive = true
    var videofile: File? = null
    var isUpdating = false
    private lateinit var testimonialSpinner: NumberSpinner

    var isOthers = false
    var product = ""
    var product_id = 0
    var crop_id = 0
    var cropName = ""

    lateinit var presenter: TestimonialFactory.TestimonialPresenter
    private lateinit var productSpinner: ProductSpinnerDialog
    private lateinit var cropSpinner: CropSpinnerDialog
    private var productList: java.util.ArrayList<ProductBean> = java.util.ArrayList()
    private var cropList: java.util.ArrayList<CropBean> = java.util.ArrayList()
    private var prodCropResponseModel: java.util.ArrayList<ProductCropResponse.ProductCropList> =
        java.util.ArrayList();
    internal var dialog: ProgressDialog? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_testimonial)
        val bundle = intent.extras
        hideSoftKeyboard()
        presenter = TestimonialPresenterImpl(this)
        setUpView()
        preference = Preference(this)
        token = preference.getString("token")
        iCross.setOnClickListener(this)
        camera_ini.setOnClickListener(this)
        spinner_testimonial.setOnClickListener(this)
        spinner_Product.setOnClickListener(this)
        spinner_crop.setOnClickListener(this)
        activateTicketBtn.setOnClickListener(this)
        rlImgFarmer.setOnClickListener(this)
        imgView.setOnClickListener(this)
        txtCaptureImage.setOnClickListener(this)

        //updateProductList()
        updateCropList()

        productSpinner = ProductSpinnerDialog(this, productList, "Select Product")

        cropSpinner = CropSpinnerDialog(this, cropList, "Select Crop")

        productSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_Product.text = item
                product = item
                product_id = productList.get(position).getpID()!!
            }
        })

        cropSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_crop.text = item
                cropName = item
                crop_id = cropList.get(position).getcID()!!
                ////////////

                product_id = 0
                product = ""
                productList.clear()
                spinner_Product.text = "Select Product"
                updateProductList(crop_id)

            }
        })

        tvMeMobile.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence, start: Int, count: Int, after: Int) {
            }

            override fun onTextChanged(s: CharSequence, start: Int, before: Int, count: Int) {

            }

            override fun afterTextChanged(s: Editable?) {
                System.out.println(s.toString())
                if (s != null) {
                    val stringData = s.toString().replace(" ", "")
                    if (s.contains(" ")) {
                        tvMeMobile.setText(stringData)
                        tvMeMobile.setSelection(stringData.length)
                        return
                    }
                    if (stringData.isNotEmpty()) {
                        if (stringData[0].equals('0') || stringData[0].equals('1') || stringData[0].equals(
                                '2'
                            )
                            || stringData[0].equals('3') || stringData[0].equals('4') || stringData[0].equals(
                                '5'
                            )
                        ) {
                            tvMeMobile.setText(
                                stringData[0].toString().replace(stringData[0].toString(), "")
                            )
                            requestFocus(tvMeMobile)
                        } else if (stringData.length != 10 && stringData.length > 0) {
                            tilMobile.error = "Enter a 10 digit number"
                            requestFocus(tvMeMobile)
                            tvMeMobile.setTextColor(Color.parseColor("#C11F1F"))
                        } else {
                            tilMobile.isErrorEnabled = false
                            tvMeMobile.setTextColor(Color.parseColor("#0EA222"))
                        }
                    }
                }

            }

        })

        /* ffmpeg = FFmpeg.getInstance(this)

         loadFFMpegBinary()*/
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
        /* cropRecordResponseModel = DatabaseManager.getCropDataFromDB(this);*/
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

    override fun onResume() {
        super.onResume()
        if (isDropBoxActive) {
            ConfirmDIalog()
        }
    }

    private fun setUpView() {
        testimonialSpinner =
            NumberSpinner(this, Utility.dataListofTestimonial, "Select Testimonial")
        testimonialSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                if (item.equals("Others")) {
                    til_other.visibility = View.VISIBLE
                    tilFarmer.visibility = View.GONE
                    isOthers = true
                    spinner_testimonial.text = item
                } else {
                    tilFarmer.visibility = View.VISIBLE
                    til_other.visibility = View.GONE
                    isOthers = false
                    testimonial_str = item
                    spinner_testimonial.text = item
                    tilFarmer.hint = item + " Name"
                }
            }
        })
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.iCross -> {
                finish()
            }
            R.id.activateTicketBtn -> {
                if (!isUpdating) {
                    onHideKeyboard()
                    uploadTicket()
                }
            }
            R.id.camera_ini -> {
                selectVideo()
            }
            R.id.imgView -> {
                selectVideo()
            }
            R.id.rlImgFarmer -> {
                selectVideo()
            }
            R.id.txtCaptureImage -> {
                selectVideo()
            }
            R.id.camerabtn -> {
                showImageInFullScreen()
            }
            R.id.spinner_Product -> {
                if (!productSpinner.isEnable)
                    productSpinner.showSpinerDialog()
            }
            R.id.spinner_crop -> {
                if (!cropSpinner.isEnable)
                    cropSpinner.showSpinerDialog()
            }
            R.id.spinner_testimonial -> {
                if (!testimonialSpinner.isEnable)
                    testimonialSpinner.showSpinerDialog()
            }

        }
    }

    private fun uploadTicket() {
        var result = validateValues()
        if (result.equals("perfect")) {
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                if (!isMyServiceRunning(TestimonialService::class.java)) {
                    isUpdating = true
                    val intent_ = Intent(this, TestimonialService::class.java)
                    intent_.putExtra(ServiceConstrants.API_TOKEN, token.toString())
                    intent_.putExtra(ServiceConstrants.TESTIMONIAL, testimonial_str.toString())
                    intent_.putExtra(ServiceConstrants.MOBILE, tvMeMobile.text.toString())
                    intent_.putExtra(ServiceConstrants.DESCRIPTION, tvMeDescription.text.toString())
                    intent_.putExtra(ServiceConstrants.CROP_ID, crop_id.toString())
                    intent_.putExtra(ServiceConstrants.PRODUCT_ID, product_id.toString())
                    intent_.putExtra(ServiceConstrants.FARMER_NAME, tvMefarmer.text.toString())
                    intent_.putExtra(ServiceConstrants.CROP, cropName.toString())
                    intent_.putExtra(ServiceConstrants.PRODUCT, product.toString())
                    intent_.putExtra(ServiceConstrants.VIDEO_FILE, videofile!!.path.toString())
                    startService(intent_)

                    super.onBackPressed()
                }

//                presenter.onSubmitPhoto(map, imagePart1)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
        } else
            makeToast(result)
    }

    /**
     * check service is running or not
     */
    private fun isMyServiceRunning(serviceClass: Class<*>): Boolean {
        val manager: ActivityManager = getSystemService(Context.ACTIVITY_SERVICE) as ActivityManager
        for (service in manager.getRunningServices(Int.MAX_VALUE)) {
            if (serviceClass.name == service.service.getClassName()) {
                return true
            }
        }
        return false
    }

    private fun validateValues(): String {
        if (isOthers) {
            testimonial_str = tvMeOther.text.toString()
        }
        if (testimonial_str.isNullOrEmpty())
            return "Please Select Testimonial"

        if (tvMeMobile.text.toString().isNullOrEmpty()) {
            return "Please Enter Mobile Number"
        } else {
            if (tvMeMobile.text.toString().length < 10)
                return "Please Enter Valid Mobile Number"
        }

        if (product_id == 0) {
            return "Please Select Product"
        }
        if (crop_id == 0) {
            return "Please Select Crop"
        }

        if (tvMefarmer.text.toString().isNullOrEmpty() && !isOthers) {
            return "Please Enter " + testimonial_str + "Name"
        }

        if (tvMeDescription.text.toString().isNullOrEmpty()) {
            return "Please Enter Description"
        }

        if (videofile == null) {
            return "Please Select Video for Testimonial"
            /* else if (!tillUrl.text.startsWith("https://www.dropbox.com/")) {
             return "Please Enter Valid URL"
         }*/
        }
        return "perfect"
    }

    private fun requestFocus(view: View) {
        if (view.requestFocus()) {
            window.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_VISIBLE)
        }
    }

    @RequiresApi(Build.VERSION_CODES.KITKAT)
    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
            viedoPicker.onActivityResult(requestCode, resultCode, data)
    }

    private fun showImageInFullScreen() {
        var intent = Intent(this, ImagePreviewActivity::class.java)
        if (ImgUrl.length > 5) {
            val imageList: ArrayList<String> = ArrayList()
            imageList.add(ImgUrl)
            intent.putExtra(ImagePreviewActivity.IMAGE_LIST, imageList);
            intent.putExtra(ImagePreviewActivity.CURRENT_ITEM, 0);
            startActivity(intent);
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
        msg.text = "Testimonial Successfully Uploaded"
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
            finish()
        }
        dialog.show()
    }

    var referenceNo = ""

    var isDropBoxActive = false

    private fun ConfirmDIalog() {
        isDropBoxActive = false
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_add_participation)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val msg = dialog.findViewById<View>(R.id.msg) as Medium
        /* val ticketNumber = dialog.findViewById<View>(R.id.tvMeTicketNumber) as Medium

         val tic_banner = dialog.findViewById<View>(R.id.tic_banner) as Medium
         ticketNumber.visibility = View.GONE
         tic_banner.text = "Reference Token"
         var tokn = preference.getString("ecn") + "/" + Utility.getCurrentDate()*/
        msg.text = "Had u submitted Testimonial on Reference Token: \n" + referenceNo
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        val cancel = dialog.findViewById<View>(R.id.cancel) as Medium
        tvOkay.text = "Yes"
        tvOkay.setOnClickListener {
            tillUrl.text = referenceNo
            dialog.dismiss()
        }
        cancel.setOnClickListener {

            dialog.dismiss()
        }

        dialog.show()
    }

    //*****************select video from camera or gallery with permission*************/
    private fun selectVideo() {
        PermissionsUtil.askPermissions(
            this,
            PermissionsUtil.CAMERA,
            PermissionsUtil.STORAGE
        ) { isGranted ->
            if (isGranted) {
                viedoPicker.selectVideo(this, "videoSelect")
            }
        }
    }

    private val viedoPicker by lazy {
        VideopickerUtil(this, object : VideoPickerListener {
            override fun onVideoPicked(path: String, tag: String) {
                videofile = File(path)
                val thumbVideo = ThumbnailUtils.createVideoThumbnail(
                    path,
                    MediaStore.Images.Thumbnails.MICRO_KIND
                )
                layout_img.setImageBitmap(thumbVideo)
                imgView.setImageResource(R.drawable.play)
            }

            override fun onError(msg: String) {
                makeToast("UnSupported Video Please Select Video Having Extension Mp4")
            }
        })
    }

    override fun onStart() {
        super.onStart()

        LocalBroadcastManager.getInstance(this).registerReceiver(
            broadcastReceiver,
            IntentFilter(ServiceConstrants.UPLOAD_VIDEO_INTENT_FILETER)
        )
    }

    override fun onPause() {
        super.onPause()
        LocalBroadcastManager.getInstance(this).unregisterReceiver(broadcastReceiver)
    }


    val broadcastReceiver: BroadcastReceiver = object : BroadcastReceiver() {
        override fun onReceive(context: Context?, intent: Intent?) {
            if (intent != null) {
                if (intent.hasExtra(ServiceConstrants.UPLOADING_DONE)) {
                    isUpdating = false
                }

                if (intent.hasExtra(ServiceConstrants.UPLOADING_FAILURE)) {
                    isUpdating = false
                }

                if (intent.hasExtra(ServiceConstrants.UPLOADING_PROGRESS)) {
                    isUpdating = true
                }

            }
        }
    }

    /*private fun loadFFMpegBinary() {
        try {
            ffmpeg.loadBinary(object: LoadBinaryResponseHandler() {
                override fun onFailure() {
                    println("Your device does not support FFmpeg.")
                }
            })
        }
        catch (e: FFmpegNotSupportedException) {
            println("Your device does not support FFmpeg.")
        }
    }

    private fun createVideoFile():File {
        val getRoot = Environment.getExternalStorageDirectory()
        val myDir = File(getRoot.getAbsolutePath() + File.separator + getResources().getString(R.string.app_name))
        if (!myDir.exists())
            myDir.mkdirs()
        videoFileName = fileTitle + "" + Date().time + ".mp4"
        val videoFile = File(myDir.getAbsolutePath(), videoFileName)
        CompressedFilePath = videoFile.getAbsolutePath()
        System.out.println("CompressedFilePath==video======" + CompressedFilePath)
        return videoFile
    }

    fun compressVideoFile(oldFilePath:String,  newFilePath:String) {
        val cmdStr = mStrCmdPre + oldFilePath + mStrCmd + newFilePath
        println("ffmpeg compress cmdStr====" + cmdStr)
        val cmdArr = cmdStr.split((" ").toRegex()).dropLastWhile { it.isEmpty() }.toTypedArray()
        try {
            ffmpeg.execute(cmdArr, object: ExecuteBinaryResponseHandler() {
                override fun onStart() {
                    dialog = ProgressDialog(activity)
                    dialog!!.setMessage(getResources().getString(R.string.Home))
                    dialog!!.setCancelable(false)
                    dialog!!.show()
                }
                override fun onProgress(message:String) {
                    println("ffmpeg compress onProgress====" + message)
                }
                override fun onFailure(message:String) {
                    println("ffmpeg compress onFailure====" + message)
                    dialog?.cancel()
                    if (newFilePath.contains("eatt")) {
                        val deletedefaultCameraFile = File(newFilePath)
                        deletedefaultCameraFile.delete()
                    }
                    makeToast("Error on Compressing file. Please try again.")
                    println("Error on uploading file. Please try again.")
                }
                override fun onSuccess(message:String) {
                    if (dialog != null)
                        dialog!!.cancel()
                    println("ffmpeg compress onSuccess====" + message)
                    videofile = File(CompressedFilePath)
                    //callImageUpload("", "", File(CompressedFilePath))
                    val thumbVideo = ThumbnailUtils.createVideoThumbnail(CompressedFilePath, MediaStore.Images.Thumbnails.MICRO_KIND)
                    layout_img.setImageBitmap(thumbVideo)
                    imgView.setImageResource(R.drawable.play)
                    println("ffmpeg deletedefaultCameraFile====" + oldFilePath)
                }
                override fun onFinish() {

                }
            })
        }
        catch (e: FFmpegCommandAlreadyRunningException) {
            makeToast("Error on Compressing file. Please try again.")
            e.printStackTrace()
        }
    }*/
}


