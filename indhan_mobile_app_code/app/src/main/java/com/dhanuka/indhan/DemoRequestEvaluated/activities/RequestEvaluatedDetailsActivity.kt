package com.dhanuka.indhan.DemoRequestEvaluated.activities

import android.app.Dialog
import android.content.Intent
import android.content.Intent.ACTION_VIEW
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.net.Uri
import android.os.Bundle
import android.view.View
import android.view.Window
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.TicketHistoryActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestEvaluated.adapters.ReviewDateByUserAdapter
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoRequestEvaluateFeedback
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoRequestEvaluatedlist
import com.dhanuka.indhan.DemoRequestEvaluated.models.EvaluateRequestModel
import com.dhanuka.indhan.Fonts.RegularEditText
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.FileUtility
import com.dhanuka.indhan.util.ImagePickerUtil
import com.dhanuka.indhan.util.ImageUtils
import com.dhanuka.indhan.util.PermissionsUtil
import com.google.android.gms.maps.*
import com.google.android.gms.maps.model.LatLng
import com.google.android.gms.maps.model.MarkerOptions
import kotlinx.android.synthetic.main.activity_request_evaluated_details.*
import kotlinx.android.synthetic.main.dialog_rate_experience.*
import okhttp3.MediaType
import okhttp3.MultipartBody
import okhttp3.RequestBody
import pl.aprilapps.easyphotopicker.DefaultCallback
import pl.aprilapps.easyphotopicker.EasyImage
import pl.aprilapps.easyphotopicker.MediaFile
import pl.aprilapps.easyphotopicker.MediaSource
import rx.Single
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.io.File
import java.util.*

class RequestEvaluatedDetailsActivity : BaseActivity(), OnMapReadyCallback {
    internal var googleMap: GoogleMap? = null
    private var file: File? = null
    private lateinit var preference: Preference

    var userFeedBackList: ArrayList<DemoRequestEvaluateFeedback> = ArrayList()
    lateinit var adapter: ReviewDateByUserAdapter
    var lat: Double = 0.0
    var lng: Double = 0.0
    var status: String = ""
    var description: String = ""
    var demoRequestId: String = ""
    private var easyImage: EasyImage? = null
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_request_evaluated_details)
        initCamera()
        //map initialize and setup
        MapsInitializer.initialize(this@RequestEvaluatedDetailsActivity)
        setupGoogleMap()
        if (intent.hasExtra(Constants.SHOW_DETAILS)) {
            val showDetails: DemoRequestEvaluatedlist =
                intent.getSerializableExtra(Constants.SHOW_DETAILS) as DemoRequestEvaluatedlist
            showDetailsOnForm(showDetails)
        }
        preference = Preference(this)
    }

    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@RequestEvaluatedDetailsActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }


    //set data for showing details
    private fun showDetailsOnForm(data: DemoRequestEvaluatedlist) {
        txtDemoNo.text = "Demo no: " + data.demo_code
        txtCropName.text = data.crop
        txtProductName.text = data.product
        txtFarmerName.text = data.farmer_name
        txtState.text = data.state
        txtDistrictName.text = data.district
        txtTahsilName.text = data.tehsil
        txtVillageName.text = data.village
        txtRecommendedDate.text = data.showing_date
        demoRequestId = "" + data.demo_request_id
        lat = data.latitude.toDouble()
        lng = data.longitude.toDouble()

        userFeedBackList.clear()
        userFeedBackList.addAll(data.demo_feedback)
        if (userFeedBackList.size > 0) {
            txtEmpty.visibility = View.GONE
        } else {
            txtEmpty.visibility = View.VISIBLE
        }
        //set review status
        setReviewAdapter(userFeedBackList)
        //navigate to location
        txtNavigateToLocation.setOnClickListener {
            if (lat != 0.0 && lng != 0.0) {
                val geoUri = "http://maps.google.com/maps?q=loc:$lat,$lng"
                val uri = String.format(Locale.ENGLISH, geoUri)
                val intent = Intent(ACTION_VIEW, Uri.parse(uri))
                startActivity(intent)
            } else {
                makeToast("Location not available for this demo request")
            }
        }
        //write feedback
        llCurrentStatus.setOnClickListener {
            showdialogForFeedback()
        }
        txtViewAssociateTicket.setOnClickListener {
            var contactNumber: String = data.contact_number
            var farmerId: String = ""
            var intent =
                Intent(this@RequestEvaluatedDetailsActivity, TicketHistoryActivity::class.java)
            intent.putExtra("contactNumber", contactNumber)
            intent.putExtra("farmerId", farmerId)
            intent.putExtra("demo_request_id", data.demo_request_id.toString())
            intent.putExtra(Constants.FARMER_FROM, "acpfarmer")
            startActivity(intent)
        }
    }

    private fun setReviewAdapter(userFeedBackList: ArrayList<DemoRequestEvaluateFeedback>) {
        rvReviewByUser.layoutManager =
            LinearLayoutManager(this)
        adapter = ReviewDateByUserAdapter(this, userFeedBackList)
        rvReviewByUser.adapter = adapter
    }

    private fun setupGoogleMap() {
        val mapFragment =
            supportFragmentManager.findFragmentById(R.id.mapUser) as SupportMapFragment
        mapFragment.getMapAsync(this)
    }

    override fun onMapReady(googleMap: GoogleMap?) {
        this.googleMap = googleMap
        if (googleMap != null) {
            setCurrentMarker(lat, lng)
        }
    }

    protected fun setCurrentMarker(latitude: Double?, longitude: Double?) {
        val latLng1 = LatLng(latitude!!, longitude!!)
        val marker = googleMap?.addMarker(
            MarkerOptions().position(latLng1).title("your current location")
        )//.icon(BitmapDescriptorFactory.fromResource(R.drawable.default_image))
        marker?.hideInfoWindow()
        googleMap?.getUiSettings()?.isZoomControlsEnabled = true
        googleMap?.getUiSettings()?.isCompassEnabled = true
        googleMap?.getUiSettings()?.setAllGesturesEnabled(true)
        googleMap?.moveCamera(CameraUpdateFactory.newLatLngZoom(LatLng(latitude, longitude), 12.0f))
    }

    //show dialog for feedback
    private fun showdialogForFeedback() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_rate_experience)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(true)

        dialog.rdgRate.setOnCheckedChangeListener { group, checkedId ->
            when (checkedId) {
                R.id.rdbExcellente -> {
                    status = "Excellent"
                }
                R.id.rdbAverage -> {
                    status = "Average"
                }
                R.id.rdbPoor -> {
                    status = "Poor"
                }
            }
        }
        dialog.imgUploadPhoto.setOnClickListener {
            PermissionsUtil.askPermissions(this, PermissionsUtil.CAMERA, PermissionsUtil.STORAGE) {
                if (it) {

                    ImagePickerUtil.pickFromCamera2(
                        easyImage,
                        this@RequestEvaluatedDetailsActivity,
                        { imageFile, _ ->
                            dialog.icPhoto.visibility = View.GONE
                            file = ImageUtils.compressToExistingFile(
                                imageFile.absolutePath,
                                imageFile
                            )
                            if(file == null){
                                file = imageFile
                            }
                            dialog.imgUploadPhoto.setImageBitmap(
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
        dialog.txtSubmit.setOnClickListener {
            if (validateForm(dialog.edtDescription)) {
                feedbackDemoRequest()
                dialog.dismiss()
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
                    onPhotosReturned(imageFiles);
                }
            })
    }

    private fun onPhotosReturned(imageFiles: Array<MediaFile>) {
        if (imageFiles.isNotEmpty()) {
            ImagePickerUtil.onActivityResult2(10000, RESULT_OK, imageFiles[0])
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

    override fun onClick(v: View?) {

    }

    fun validateForm(edtDescription: RegularEditText): Boolean {
        description = edtDescription.text.toString()
        if (status.isNullOrBlank()) {
            makeToast("please select status")
            return false
        } else if (description.isNullOrBlank()) {
            makeToast("please enter description")
            return false
        } else if (file == null) {
            makeToast("please upload image")
            return false
        }
        return true
    }

    private fun feedbackDemoRequest() {
        showProgress()
        val token: String = preference.getString("token")
        val query = HashMap<String, RequestBody>()
        query.put("apiToken", getRequestBody(token))
        query.put("description", getRequestBody(description))
        query.put("status", getRequestBody(status))
        query.put("demo_request_id", getRequestBody(demoRequestId))
        val model = EvaluateRequestModel(
            id = System.currentTimeMillis().toInt(),
            demo_request_id = demoRequestId,
            description = description,
            feedbackImage = file?.path ?: "",
            feedbackImageName = file?.name ?: "",
            status = status
        )
//        saveFeedbackDataLocally(model)
        saveDataToServer(query)
    }

    private fun saveDataToServer(query: HashMap<String, RequestBody>) {
        if (file != null) {
            val fileBody1 = RequestBody.create(MediaType.parse("image/*"), file!!)
            val imagePart1: MultipartBody.Part =
                MultipartBody.Part.createFormData("feedbackImage", file?.name, fileBody1)

            RetrofitClient.mvpClient
                .create(ApiServiceRequest::class.java).demoFeedbackApi(query, imagePart1)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<CommonAtpResponse>() {
                    override fun onCompleted() {
                        hideProgress()
                    }

                    override fun onError(e: Throwable) {
                        makeToast(
                            e.localizedMessage
                                ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                        )
                        hideProgress()
                    }

                    override fun onNext(bean: CommonAtpResponse) {
                        hideProgress()
                        makeToast(bean.message)

                    }
                }
                )
        } else {
            val fileBody1 = RequestBody.create(MediaType.parse("image/*"), "")
            val imagePart1: MultipartBody.Part =
                MultipartBody.Part.createFormData("feedbackImage", "", fileBody1)
            RetrofitClient.mvpClient
                .create(ApiServiceRequest::class.java).demoFeedbackApi(query, imagePart1)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<CommonAtpResponse>() {
                    override fun onCompleted() {
                        hideProgress()
                    }

                    override fun onError(e: Throwable) {
                        makeToast(
                            e.localizedMessage
                                ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                        )
                        hideProgress()
                    }

                    override fun onNext(bean: CommonAtpResponse) {
                        hideProgress()
                        makeToast(bean.message)
                    }
                }
                )
        }
    }

    private fun saveFeedbackDataLocally(model: EvaluateRequestModel) {
        Single.fromCallable {
            val appDatabase = DatabaseClient.getInstance(this).appDatabase
            appDatabase.evaluateDemoRequest().insert(model)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe().run {
            makeToast("Feedback saved offline successfully.")
            hideProgress()
        }
    }

    private fun getRequestBody(value: String): RequestBody {
        return RequestBody.create(MediaType.parse("application/json; charset=utf-8"), value)
    }


}
