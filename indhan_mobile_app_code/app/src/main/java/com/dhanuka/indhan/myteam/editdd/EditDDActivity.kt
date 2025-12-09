package com.dhanuka.indhan.myteam.editdd

import android.app.Dialog
import android.content.Intent
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.view.View
import android.view.Window
import android.widget.ImageView
import androidx.activity.result.ActivityResult
import androidx.activity.result.ActivityResultCallback
import androidx.activity.result.contract.ActivityResultContracts
import com.bumptech.glide.Glide
import com.bumptech.glide.load.engine.DiskCacheStrategy
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.TestimonialList.filter.DDBean
import com.dhanuka.indhan.util.ImagePickerUtil
import com.dhanuka.indhan.util.ImageUtils
import com.dhanuka.indhan.util.PermissionsUtil
import kotlinx.android.synthetic.main.activity_edit_dd.*
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

class EditDDActivity : BaseActivity() {
    override fun onClick(v: View?) {

    }

    private lateinit var data: DDBean.Data

    private var file: File? = null

    private lateinit var preference: Preference
    private var easyImage: EasyImage? = null
    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@EditDDActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initCamera()
        setContentView(R.layout.activity_edit_dd)
        preference = Preference(this)
        data = intent.getSerializableExtra("data") as DDBean.Data
        txtFullName.text = data.name
        txtContactNo.text = data.ddMobileNo
        edtAltContactsNO.setText(data.ddAltMobileNo)
        Glide.with(this)
            .asBitmap()
            .apply(
                RequestOptions()
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .skipMemoryCache(true)
            )
            .load(data.ddImage)
            .into(imgPicture)


        if (data.ddImage != "") {
            imgCross.visibility = View.VISIBLE
            imgCamera.visibility = View.GONE
        }

        rlPicture.setOnClickListener {
            if (file == null && data.ddImage == "") {
                PermissionsUtil.askPermissions(
                    this,
                    PermissionsUtil.CAMERA,
                    PermissionsUtil.STORAGE
                ) {
                    if (it) {
                        ImagePickerUtil.pickFromCamera2(
                            easyImage,
                            this@EditDDActivity,
                            { imageFile, _ ->
                                file = ImageUtils.compressToExistingFile(
                                    imageFile.absolutePath,
                                    imageFile
                                )
                                if(file == null){
                                    file = imageFile
                                }
                                Glide.with(this@EditDDActivity)
                                    .asBitmap()
                                    .apply(
                                        RequestOptions().diskCacheStrategy(DiskCacheStrategy.NONE)
                                            .skipMemoryCache(true)
                                    )
                                    .load(file?.absoluteFile)
                                    .into(imgPicture)
                                imgPicture.visibility = View.VISIBLE
                                imgCross.visibility = View.VISIBLE
                                imgCamera.visibility = View.GONE
                            }, ""
                        )
                    }
                }
            } else {
                showdialogone()
            }
        }

        imgCross.setOnClickListener {
            file = null
            data.ddImage = ""
            imgCross.visibility = View.GONE
            imgCamera.visibility = View.VISIBLE
            imgPicture.setImageDrawable(null)
        }

        btnNext.setOnClickListener {
            getUpdateDD()
        }

    }


    var someActivityResultLauncher = registerForActivityResult(
        ActivityResultContracts.StartActivityForResult(),
        ActivityResultCallback { result: ActivityResult ->
            if (result.resultCode == RESULT_OK) {
                // There are no request codes
                val data = result.data
            }
        } as ActivityResultCallback<ActivityResult>)

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

        if (file != null) {
            Glide.with(this)
                .load(file?.absoluteFile)
                .apply(requestOptions)
                .apply(
                    RequestOptions()
                        .diskCacheStrategy(DiskCacheStrategy.NONE)
                        .skipMemoryCache(true)
                )
//                    .centerCrop()
//                    .crossFade()
                .into(msg)

//        msg.text = "Testimonial Successfully Uploaded"

        } else {
            Glide.with(this)
                .load(data.ddImage)
                .apply(requestOptions)
                .apply(
                    RequestOptions()
                        .diskCacheStrategy(DiskCacheStrategy.NONE)
                        .skipMemoryCache(true)
                )
//                    .centerCrop()
//                    .crossFade()
                .into(msg)
        }
        dialog.show()
    }

    private fun getRequestBody(value: String): RequestBody {
        return RequestBody.create(MediaType.parse("application/json; charset=utf-8"), value)
    }

    private fun getUpdateDD() {
        showProgress()
        val map = HashMap<String, RequestBody>()
        map["api_token"] = getRequestBody(preference.getString("token"))
        map["dd_id"] = getRequestBody(data.ddId)
        map["dd_alt_mobile_no"] = getRequestBody(edtAltContactsNO.text.toString())

        if (file != null) {
            val fileBody1 = RequestBody.create(MediaType.parse("image/*"), file!!)
            val imagePart1: MultipartBody.Part =
                MultipartBody.Part.createFormData("dd_image", file?.name, fileBody1)

            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .editDD(map, imagePart1).subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<EditBean>() {
                    override fun onCompleted() {}


                    override fun onError(e: Throwable) {
                        hideProgress()

                    }

                    override fun onNext(listBean: EditBean) {
                        hideProgress()
                        if (listBean.statusCode == 200) {
                            makeToast("Updated successfully")
                            finish()
                        } else {

                        }
                    }
                })

        } else {
            val fileBody1 = RequestBody.create(MediaType.parse("image/*"), "")
            val imagePart1: MultipartBody.Part =
                MultipartBody.Part.createFormData("dd_image", "", fileBody1)

            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .editDD(map, imagePart1).subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<EditBean>() {
                    override fun onCompleted() {}


                    override fun onError(e: Throwable) {
                        hideProgress()

                    }

                    override fun onNext(listBean: EditBean) {
                        hideProgress()
                        if (listBean.statusCode == 200) {
                            makeToast("Updated successfully")
                            finish()
                        } else {

                        }
                    }
                })
        }
    }

    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<out String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        PermissionsUtil.onRequestPermissionsResult(requestCode, permissions, grantResults)
    }
    @Deprecated("Deprecated in Java")
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


}


