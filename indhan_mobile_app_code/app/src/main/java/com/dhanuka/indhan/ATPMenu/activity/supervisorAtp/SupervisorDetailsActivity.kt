package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp

import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.text.TextUtils
import android.view.View
import com.bumptech.glide.Glide
import com.dhanuka.indhan.ATPMenu.activity.ATPBean
import com.dhanuka.indhan.ATPMenu.activity.activities.InvitedFarmerListActivity
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryAtpResponse
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Constants.Constants.IMAGEBASEURL
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.Utility
import com.dhanuka.indhan.validatefarmers.ValidateFarmersActivity
import com.google.android.gms.maps.*
import com.google.android.gms.maps.model.LatLng
import com.google.android.gms.maps.model.MarkerOptions
import com.himangi.imagepreview.ImagePreviewActivity
import kotlinx.android.synthetic.main.activity_atpdetails.*
import kotlinx.android.synthetic.main.activity_supervisor_details.*
import kotlinx.android.synthetic.main.activity_supervisor_details.appBarBottom
import kotlinx.android.synthetic.main.activity_supervisor_details.date
import kotlinx.android.synthetic.main.activity_supervisor_details.iCross
import kotlinx.android.synthetic.main.activity_supervisor_details.ivShareMeetingDetail
import kotlinx.android.synthetic.main.activity_supervisor_details.relMeetingData
import kotlinx.android.synthetic.main.activity_supervisor_details.tvMeetingDateTime
import kotlinx.android.synthetic.main.activity_supervisor_details.tvMeetingDetail
import kotlinx.android.synthetic.main.activity_supervisor_details.tvMonitorName
import kotlinx.android.synthetic.main.activity_supervisor_details.tvPresenterName
import kotlinx.android.synthetic.main.activity_supervisor_details.tv_crop
import kotlinx.android.synthetic.main.activity_supervisor_details.tv_district
import kotlinx.android.synthetic.main.activity_supervisor_details.tv_ds
import kotlinx.android.synthetic.main.activity_supervisor_details.tv_farmer
import kotlinx.android.synthetic.main.activity_supervisor_details.tv_invited
import kotlinx.android.synthetic.main.activity_supervisor_details.tv_language
import kotlinx.android.synthetic.main.activity_supervisor_details.tv_product
import kotlinx.android.synthetic.main.activity_supervisor_details.tv_product1
import kotlinx.android.synthetic.main.activity_supervisor_details.tv_product2
import kotlinx.android.synthetic.main.activity_supervisor_details.tv_register_farmer
import kotlinx.android.synthetic.main.activity_supervisor_details.tv_state
import kotlinx.android.synthetic.main.activity_supervisor_details.tv_tehsil
import kotlinx.android.synthetic.main.activity_supervisor_details.tv_village
import kotlinx.android.synthetic.main.activity_supervisor_details.txtCreatedDate
import kotlinx.android.synthetic.main.activity_supervisor_details.txtDDName
import kotlinx.android.synthetic.main.activity_supervisor_details.txtInviteFarmerCount
import kotlinx.android.synthetic.main.activity_supervisor_details.view
import java.util.*
import kotlin.math.roundToInt


class SupervisorDetailsActivity : BaseActivity(), OnMapReadyCallback {
    override fun onClick(v: View?) {

    }

    override fun onMapReady(p0: GoogleMap?) {
        this.googleMap = googleMap
        if (googleMap != null) {
            //setCurrentMarker(lat, lng)
        }
    }

    private lateinit var atpListData: ATPBean.Data
    private lateinit var myATPListData: ATPBean.Data
    private lateinit var activeATP: SupervisorActivateBean.Data
    private lateinit var historyATP: TicketHistoryAtpResponse.Data
    private var googleMap: GoogleMap? = null


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_supervisor_details)

        //map initialize and setup
        MapsInitializer.initialize(this)
        setupGoogleMap()

        iCross.setOnClickListener {
            finish()
        }

        when {
            intent.hasExtra(Constants.ATP_DETAIL) -> {
                atpListData = intent.getSerializableExtra(Constants.ATP_DETAIL) as ATPBean.Data
                txtFirstButton.text = "Add to My ATP"
                appBarBottom.visibility = View.GONE
                tvActivityDuration.visibility = View.GONE
                llArrivalDepartureTime.visibility = View.GONE
                setView(atpListData)
            }
            intent.hasExtra("fromMyATP") -> {
                myATPListData = intent.getSerializableExtra("fromMyATP") as ATPBean.Data
                tvActivityDuration.visibility = View.GONE
                llArrivalDepartureTime.visibility = View.GONE
                setViewOne(myATPListData)
            }
            intent.hasExtra("fromActiveATP") -> {
                activeATP =
                    intent.getSerializableExtra("fromActiveATP") as SupervisorActivateBean.Data
                tvActivityDuration.visibility = View.GONE
                llArrivalDepartureTime.visibility = View.GONE
                setViewTwo(activeATP)
            }

            intent.hasExtra("fromHistoryATP") -> {
                historyATP =
                    intent.getSerializableExtra("fromHistoryATP") as TicketHistoryAtpResponse.Data
                img_src.visibility = View.GONE
                setViewThree(historyATP)

            }
        }


    }

    private fun setupGoogleMap() {
        val mapFragment =
            supportFragmentManager.findFragmentById(R.id.mapUser) as SupportMapFragment
        mapFragment.getMapAsync(this)
    }

    private fun setCurrentMarker(latitude: Double?, longitude: Double?) {
        val latLng1 = LatLng(latitude!!, longitude!!)
        val marker = googleMap?.addMarker(
            MarkerOptions().position(latLng1).title("your current location")
        )//.icon(BitmapDescriptorFactory.fromResource(R.drawable.default_image))
        marker?.hideInfoWindow()
        googleMap?.uiSettings?.isZoomControlsEnabled = true
        googleMap?.uiSettings?.isCompassEnabled = true
        googleMap?.uiSettings?.setAllGesturesEnabled(true)
        googleMap?.moveCamera(CameraUpdateFactory.newLatLngZoom(LatLng(latitude, longitude), 12.0f))
    }

    private fun shareViaEmail(
        message: String
    ) {
        try {
            val intent = Intent(Intent.ACTION_SEND)
            intent.type = "text/html"
            intent.putExtra(Intent.EXTRA_EMAIL, arrayOf(""))
            intent.putExtra(Intent.EXTRA_SUBJECT, "Meeting Detail")
            intent.putExtra(Intent.EXTRA_TEXT, message)
            intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
            startActivityForResult(intent, 3244)
        } catch (e: Exception) {
            makeToast("There is no email client installed.")
        }
    }

    private fun setView(model: ATPBean.Data) {

        txtInviteFarmerCount.setOnClickListener {
            val intent = Intent(this, InvitedFarmerListActivity::class.java)
            intent.putExtra("atpId", model.plan_atp_id)
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
        }
        txtInviteFarmerCount.text = model.total_invited_farmers


        txtNavigateToLocation.setOnClickListener {
            if (model.latitude != 0.0 && model.longitude != 0.0) {
                val geoUri = "http://maps.google.com/maps?q=loc:${model.latitude},${model.latitude}"
                val uri = String.format(Locale.ENGLISH, geoUri)
                val intent = Intent(Intent.ACTION_VIEW, Uri.parse(uri))
                startActivity(intent)
            } else {
                makeToast("Location not available for this ticket")
            }
        }

        when (model.status) {
            "0" -> txtStatus.text = "Upcoming"
            "1" -> txtStatus.text = "Active"
            "2" -> txtStatus.text = "Closed"
            else -> txtStatus.text = "Pending"
        }
        if (!TextUtils.isEmpty(model.meeting_detail)) {
            relMeetingData.visibility = View.VISIBLE
            tvMeetingDetail.text = "Venue - ".plus(model.meeting_detail)
            if (!TextUtils.isEmpty(model.meeting_date)) {
                tvMeetingDateTime.text = "Date Time -> ${Utility.getDate(model.meeting_date!!)}"
            } else {
                tvMeetingDateTime.text = ""
            }
//            tvMeetingDateTime.text = "Date Time -> ${Utility.getDateForerrorView(model.meeting_date ?: "")}"
            tvPresenterName.visibility = View.GONE
            tvMonitorName.visibility = View.INVISIBLE
            ivShareMeetingDetail.setOnClickListener {
                val shareData = "Meeting Detail \n\n${tvMeetingDetail.text.toString().trim()}" +
                        "\n${tvMeetingDateTime.text.toString().trim()}"
                shareViaEmail(shareData)
            }
        }
        txtDDName.text = model.dd_name

        txtCreatedDate.text = Utility.getDateForerrorViewNew(model.created_at)

        tv_register_farmer.setOnClickListener {
            val intent = Intent(this, ValidateFarmersActivity::class.java)
            intent.putExtra("data", "Validate Farmers")
            intent.putExtra("key", "validate_farmer")
            intent.putExtra("atpId", model.ticket_id.toString())
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
        }
        tv_invited.setOnClickListener {
            val intent =
                Intent(this@SupervisorDetailsActivity, InvitedFarmerListActivity::class.java)
            intent.putExtra("atpId", model.plan_atp_id)
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)

        }



        when {
            model.product.size == 1 -> {

                tv_crop.text = model.crop[0].crop
                tv_product.text = model.product[0].product

                tv_crop1.text = "- -"
                tv_product1.text = "- -"

                tv_crop2.text = "- -"
                tv_product2.text = "- -"

            }
            model.product.size == 2 -> {

                tv_crop.text = model.crop[0].crop
                tv_product.text = model.product[0].product

                tv_crop1.text = model.crop[1].crop
                tv_product1.text = model.product[1].product

                tv_crop2.text = "- -"
                tv_product2.text = "- -"

            }
            model.product.size == 3 -> {

                tv_crop.text = model.crop[0].crop
                tv_product.text = model.product[0].product

                tv_crop1.text = model.crop[1].crop
                tv_product1.text = model.product[1].product

                tv_crop2.text = model.crop[2].crop
                tv_product2.text = model.product[2].product
            }
        }





        if (model.plan_date.isNullOrEmpty()) date.text = "- -"
        else {
            if (model.plan_date.length > 10) {
                date.text = Utility.getDateForerrorView(model.plan_date)


            } else {
                date.text = Utility.getDateForView(model.plan_date)

            }
        }



        if (model.language_id.isNullOrEmpty()) tv_language.text = "- -"
        else tv_language.text = model.language_id



        if (model.data_source_id.isNullOrEmpty()) tv_ds.text = "- -"
        else tv_ds.text = model.data_source_id

        if (model.state.isNullOrEmpty()) tv_state.text = "- -"
        else tv_state.text = model.state

        if (model.district.isNullOrEmpty()) tv_district.text = "- -"
        else tv_district.text = model.district

        if (model.tehsil.isNullOrEmpty()) tv_tehsil.text = "- -"
        else tv_tehsil.text = model.tehsil

        if (model.village.isNullOrEmpty()) tv_village.text = "- -"
        else tv_village.text = model.village

        if (model.total_farmers.isNullOrEmpty()) tv_register_farmer.text = "- -"
        else tv_register_farmer.text = model.total_farmers

        if (model.total_invited_farmers.isNullOrEmpty()) tv_invited.text = "- -"
        else tv_invited.text = model.total_invited_farmers

        tv_farmer.text = model.expected_farmer.toString()

    }

    private fun setViewOne(model: ATPBean.Data) {

        txtInviteFarmerCount.setOnClickListener {
            val intent = Intent(this, InvitedFarmerListActivity::class.java)
            intent.putExtra("atpId", model.plan_atp_id)
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
        }

        txtInviteFarmerCount.text = model.total_invited_farmers

        txtNavigateToLocation.setOnClickListener {
            if (model.latitude != 0.0 && model.longitude != 0.0) {
                val geoUri = "http://maps.google.com/maps?q=loc:${model.latitude},${model.latitude}"
                val uri = String.format(Locale.ENGLISH, geoUri)
                val intent = Intent(Intent.ACTION_VIEW, Uri.parse(uri))
                startActivity(intent)
            } else {
                makeToast("Location not available for this ticket")
            }
        }
        if (!TextUtils.isEmpty(model.meeting_detail)) {
            relMeetingData.visibility = View.VISIBLE
            tvMeetingDetail.text = "Venue - ".plus(model.meeting_detail)
            if (!TextUtils.isEmpty(model.meeting_date)) {
                tvMeetingDateTime.text = "Date Time -> ${Utility.getDate(model.meeting_date!!)}"
            } else {
                tvMeetingDateTime.text = ""
            }
            tvPresenterName.visibility = View.GONE
            tvMonitorName.visibility = View.INVISIBLE
            ivShareMeetingDetail.setOnClickListener {
                val shareData = "Meeting Detail \n\n${tvMeetingDetail.text.toString().trim()}" +
                        "\n${tvMeetingDateTime.text.toString().trim()}"
                shareViaEmail(shareData)
            }
        }

        when {
            model.status == "0" -> txtStatus.text = "Upcoming"
            model.status == "1" -> txtStatus.text = "Active"
            model.status == "2" -> txtStatus.text = "Closed"
            else -> txtStatus.text = "Pending"
        }

        txtDDName.text = model.dd_name


        txtCreatedDate.text = Utility.getDateForerrorViewNew(model.created_at)
        tv_register_farmer.setOnClickListener {
            val intent = Intent(this, ValidateFarmersActivity::class.java)
            intent.putExtra("data", "Validate Farmers")
            intent.putExtra("key", "validate_farmer")
            intent.putExtra("atpId", model.ticket_id.toString())
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
        }

        tv_invited.setOnClickListener {
            val intent =
                Intent(this@SupervisorDetailsActivity, InvitedFarmerListActivity::class.java)
            intent.putExtra("atpId", model.plan_atp_id)
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)

        }
        when {
            model.product.size == 1 -> {

                tv_crop.text = model.crop[0].crop
                tv_product.text = model.product[0].product

                tv_crop1.text = "- -"
                tv_product1.text = "- -"

                tv_crop2.text = "- -"
                tv_product2.text = "- -"

            }
            model.product.size == 2 -> {

                tv_crop.text = model.crop[0].crop
                tv_product.text = model.product[0].product

                tv_crop1.text = model.crop[1].crop
                tv_product1.text = model.product[1].product

                tv_crop2.text = "- -"
                tv_product2.text = "- -"

            }
            model.product.size == 3 -> {

                tv_crop.text = model.crop[0].crop
                tv_product.text = model.product[0].product

                tv_crop1.text = model.crop[1].crop
                tv_product1.text = model.product[1].product

                tv_crop2.text = model.crop[2].crop
                tv_product2.text = model.product[2].product
            }
        }





        if (model.plan_date.isNullOrEmpty()) date.text = "- -"
        else {
            if (model.plan_date.length > 10) {
                date.text = Utility.getDateForerrorView(model.plan_date)


            } else {
                date.text = Utility.getDateForView(model.plan_date)

            }
        }



        if (model.language_id.isNullOrEmpty()) tv_language.text = "- -"
        else tv_language.text = model.language_id



        if (model.data_source_id.isNullOrEmpty()) tv_ds.text = "- -"
        else tv_ds.text = model.data_source_id

        if (model.state.isNullOrEmpty()) tv_state.text = "- -"
        else tv_state.text = model.state

        if (model.district.isNullOrEmpty()) tv_district.text = "- -"
        else tv_district.text = model.district

        if (model.tehsil.isNullOrEmpty()) tv_tehsil.text = "- -"
        else tv_tehsil.text = model.tehsil

        if (model.village.isNullOrEmpty()) tv_village.text = "- -"
        else tv_village.text = model.village

        if (model.total_farmers.isNullOrEmpty()) tv_register_farmer.text = "- -"
        else tv_register_farmer.text = model.total_farmers

        if (model.total_invited_farmers.isNullOrEmpty()) tv_invited.text = "- -"
        else tv_invited.text = model.total_invited_farmers

        tv_farmer.text = model.expected_farmer.toString()
    }

    private fun setViewTwo(model: SupervisorActivateBean.Data) {
        // arrival departure
        txtInviteFarmerCount.text = model.invited_farmer_count

        txtInviteFarmerCount.setOnClickListener {
            val intent = Intent(this, InvitedFarmerListActivity::class.java)
            intent.putExtra("atpId", model.plan_atp_id.toString())
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
        }

        txtNavigateToLocation.setOnClickListener {
            if (model.latitude != 0.0 && model.longitude != 0.0) {
                val geoUri = "http://maps.google.com/maps?q=loc:${model.latitude},${model.latitude}"
                val uri = String.format(Locale.ENGLISH, geoUri)
                val intent = Intent(Intent.ACTION_VIEW, Uri.parse(uri))
                startActivity(intent)
            } else {
                makeToast("Location not available for this ticket")
            }
        }

        txtStatus.text = "Active"


        txtDDName.text = model.ddName

        txtCreatedDate.text = model.datetimeFormat

        if (model.feedbackImage != null) {
            img_src.visibility = View.VISIBLE
            Glide.with(this)
                .asBitmap()
                .load(model.feedbackImage)
                .into(img_src)

        } else {
            img_src.visibility = View.GONE
        }

        img_src.setOnClickListener {
            val intent = Intent(this, ImagePreviewActivity::class.java)
            if (model.feedbackImage.length > 5) {
                val imageList: ArrayList<String> = ArrayList()
                imageList.add(model.feedbackImage)
                intent.putExtra(ImagePreviewActivity.IMAGE_LIST, imageList)
                intent.putExtra(ImagePreviewActivity.CURRENT_ITEM, 0)
                startActivity(intent)
            }
        }

        tv_register_farmer.setOnClickListener {
            val intent = Intent(this, ValidateFarmersActivity::class.java)
            intent.putExtra("data", "Validate Farmers")
            intent.putExtra("key", "validate_farmer")
            intent.putExtra("atpId", model.tID.toString())
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
        }
        tv_invited.setOnClickListener {
//            val intent = Intent(this@SupervisorDetailsActivity, InvitedFarmerListActivity::class.java)
//            intent.putExtra("atpId", model.pl)
//            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
//            startActivity(intent)

        }

        when {
            model.crop2 == null -> {

                tv_crop.text = model.crop1
                tv_product.text = model.product1

                tv_crop1.text = "- -"
                tv_product1.text = "- -"

                tv_crop2.text = "- -"
                tv_product2.text = "- -"

            }
            model.crop3 == null -> {

                tv_crop.text = model.crop1
                tv_product.text = model.product1

                tv_crop1.text = model.crop2
                tv_product1.text = model.product2.toString()

                tv_crop2.text = "- -"
                tv_product2.text = "- -"

            }

            model.product3 != null -> {

                tv_crop.text = model.crop1
                tv_product.text = model.product1

                tv_crop1.text = model.crop2
                tv_product1.text = model.product2.toString()

                tv_crop2.text = model.crop3
                tv_product2.text = model.product3.toString()
            }
        }





        if (model.datetime.isNullOrEmpty()) date.text = "- -"
        else {
            if (model.datetime.length > 10) {
                date.text = model.datetimeFormat


            } else {
                date.text = model.datetimeFormat

            }
        }



        if (model.language.isNullOrEmpty()) tv_language.text = "- -"
        else tv_language.text = model.language



        if (model.dataSource.isNullOrEmpty()) tv_ds.text = "- -"
        else tv_ds.text = model.dataSource

        if (model.state.isNullOrEmpty()) tv_state.text = "- -"
        else tv_state.text = model.state

        if (model.district.isNullOrEmpty()) tv_district.text = "- -"
        else tv_district.text = model.district

        if (model.tehsil.isNullOrEmpty()) tv_tehsil.text = "- -"
        else tv_tehsil.text = model.tehsil

        if (model.village.isNullOrEmpty()) tv_village.text = "- -"
        else tv_village.text = model.village

        tv_register_farmer.text = model.totalFarmers.toString()

        tv_invited.text = model.invited_farmer_count.toString()

        tv_farmer.text = model.expectedFarmers.toString()


    }

    private fun setViewThree(model: TicketHistoryAtpResponse.Data) {
        txtInviteFarmerCount.visibility = View.VISIBLE
        view.visibility = View.VISIBLE
        txtInviteFarmerCount.text = model.invited_farmer_count.toString()
        txtInviteFarmerCount.setOnClickListener {
            val intent = Intent(this, InvitedFarmerListActivity::class.java)
            intent.putExtra("atpId", model.plan_atp_id.toString())
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
        }

        txtDDName.text = model.dd_name
        img_src.visibility = View.GONE

        if (!TextUtils.isEmpty(model.departure_time)) {
            llArrivalDepartureTime.visibility = View.VISIBLE
            tvActivityDuration.visibility = View.VISIBLE
        } else {
            llArrivalDepartureTime.visibility = View.GONE
            tvActivityDuration.visibility = View.GONE
        }

        if (!TextUtils.isEmpty(model.arival_uploaded_file)) {
            ivArrivalImage.visibility = View.VISIBLE
            Glide.with(this)
                .asBitmap()
                .load(IMAGEBASEURL + model.arival_uploaded_file)
                .into(ivArrivalImage)
        }

        if (!TextUtils.isEmpty(model.arival_time)) {

            "Arrival : ${
                Utility.getDateForArrivalDeparture(
                    model.arival_time,
                    "yyyy-MM-dd HH:mm:ss",
                    "dd MMM yyyy hh:mm a"
                )
            }".also { tvArrivalText.text = it }
        }

        if (!TextUtils.isEmpty(model.departure_time)) {
            "Departure : ${
                Utility.getDateForArrivalDeparture(
                    model.departure_time,
                    "yyyy-MM-dd HH:mm:ss",
                    "dd MMM yyyy hh:mm a"
                )
            }".also { tvDepartureText.text = it }
        }

        if (!TextUtils.isEmpty(model.departure_uploaded_file)) {
            ivDepartureImage.visibility = View.VISIBLE
            Glide.with(this)
                .asBitmap()
                .load(IMAGEBASEURL + model.departure_uploaded_file)
                .into(ivDepartureImage)

        }

        if (!TextUtils.isEmpty(model.duration)) {
            var duration = ""
            duration = if (model.duration.contains(".")) {
                "- ${model.duration.split(".")[0].toInt()} hours " +
                        "0.${model.duration.split(".")[1]}".toDouble().times(60)
                            .toInt() + " minutes"
            } else {
                "- ${(model.duration.toDouble().roundToInt())} hours"
            }
            tvActivityDuration.text = "Activity Duration : $duration"
        }


        img_src.setOnClickListener {
            val intent = Intent(this, ImagePreviewActivity::class.java)
            if (model.feedbackImage.length > 5) {
                val imageList: ArrayList<String> = ArrayList()
                imageList.add(model.feedbackImage)
                intent.putExtra(ImagePreviewActivity.IMAGE_LIST, imageList)
                intent.putExtra(ImagePreviewActivity.CURRENT_ITEM, 0)
                startActivity(intent)
            }
        }

        txtStatus.visibility = View.GONE
        fragmentMap.visibility = View.GONE

        txtCreatedDate.text = model.datetime_format
        tv_register_farmer.setOnClickListener {
            val intent = Intent(this, ValidateFarmersActivity::class.java)
            intent.putExtra("data", "Validate Farmers")
            intent.putExtra("key", "validate_farmer")
            intent.putExtra("atpId", model.tid.toString())
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
        }
        tv_invited.setOnClickListener {
//            val intent = Intent(this@SupervisorDetailsActivity, InvitedFarmerListActivity::class.java)
//            intent.putExtra("atpId", model.plan_atp_id)
//            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
//            startActivity(intent)

        }
        when {
            model.crop_2 == null -> {

                tv_crop.text = model.crop_1
                tv_product.text = model.product_1

                tv_crop1.text = "- -"
                tv_product1.text = "- -"

                tv_crop2.text = "- -"
                tv_product2.text = "- -"

            }
            model.crop_3 == null -> {

                tv_crop.text = model.crop_1
                tv_product.text = model.product_1

                tv_crop1.text = model.crop_2
                tv_product1.text = model.product_2

                tv_crop2.text = "- -"
                tv_product2.text = "- -"

            }

            model.product_3 != null -> {

                tv_crop.text = model.crop_1
                tv_product.text = model.product_1

                tv_crop1.text = model.crop_2
                tv_product1.text = model.product_2

                tv_crop2.text = model.crop_3
                tv_product2.text = model.product_3
            }
        }





        if (model.datetime.isNullOrEmpty()) date.text = "- -"
        else {
            if (model.datetime.length > 10) {
                date.text = model.datetime_format
            } else {
                date.text = Utility.getDateForView(model.datetime)

            }
        }


        if (model.language.isNullOrEmpty()) tv_language.text = "- -"
        else tv_language.text = model.language

        if (model.data_source.isNullOrEmpty()) tv_ds.text = "- -"
        else tv_ds.text = model.data_source

        if (model.state.isNullOrEmpty()) tv_state.text = "- -"
        else tv_state.text = model.state

        if (model.district.isNullOrEmpty()) tv_district.text = "- -"
        else tv_district.text = model.district

        if (model.tehsil.isNullOrEmpty()) tv_tehsil.text = "- -"
        else tv_tehsil.text = model.tehsil

        if (model.village.isNullOrEmpty()) tv_village.text = "- -"
        else tv_village.text = model.village


        tv_register_farmer.text = model.total_farmers.toString()

        tv_invited.text = model.invited_farmer_count.toString()

        tv_farmer.text = model.expected_farmers.toString()

    }


}
