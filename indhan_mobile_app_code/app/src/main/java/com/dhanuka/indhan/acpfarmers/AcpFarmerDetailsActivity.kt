package com.dhanuka.indhan.acpfarmers

import android.content.Intent
import android.location.Address
import android.location.Geocoder
import android.net.Uri
import android.os.Bundle
import android.text.SpannableString
import android.text.style.UnderlineSpan
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.bumptech.glide.Glide
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.TicketHistoryActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.R
import com.dhanuka.indhan.visithistory.VisitHistoryFarmerActivity
import kotlinx.android.synthetic.main.activity_acp_farmer_details.*
import java.util.*
import kotlin.collections.ArrayList

class AcpFarmerDetailsActivity : BaseActivity() {
    var lat: Double = 0.0
    var lng: Double = 0.0

    private lateinit var response: ACPFarmerBean.Data.ACPDetail
    var leadsArray: ArrayList<ACPFarmerBean.Data.ACPDetail.Leads> = ArrayList()
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_acp_farmer_details)
        if (intent.hasExtra("data")) {
            response = intent.getSerializableExtra("data") as ACPFarmerBean.Data.ACPDetail
            setView(response)
        }


        txtCallNow.setOnClickListener {
            val intent = Intent(Intent.ACTION_DIAL)
            intent.data = Uri.parse("tel: ${response.contactNumber}")
            startActivity(intent)
        }



        txtNoOfAvgVisit.setOnClickListener {
            var i = Intent(this, VisitHistoryFarmerActivity::class.java)
            i.putExtra("key", response.farmerId)
            startActivity(i)


        }


        txtAvgVisit.setOnClickListener {
            var i = Intent(this, VisitHistoryFarmerActivity::class.java)
            i.putExtra("key", response.farmerId)
            startActivity(i)

        }

        txtNoFarmer.setOnClickListener {
            //showdialogTwo()
        }
        txtFarmers.setOnClickListener {
            //showdialogTwo()
        }


    }


    private fun setView(response: ACPFarmerBean.Data.ACPDetail) {
//        txtCallNow.setOnClickListener {
//            val intent = Intent(Intent.ACTION_DIAL)
//            intent.data = Uri.parse("tel: ${response.contactNumber}")
//        }

        val newName = if (response.contactNumber.length > 5) response.contactNumber.substring(
            0,
            6
        ) + "XXXX" else "XXXXXXXXXX"

        txtUserName.text = response.farmerName
        txtID.text = "S/o " + response.fatherName
        txtPhoneNo.text = newName
        txtNoFarmer.text = response.acres
        txtNoOfAvgVisit.text = response.acpVisit.toString()

//        txtAddress.text=response
        txtState.text = response.state
        txtDistrict.text = response.district
        tehsil.text = response.tehsil

        val content1 = SpannableString("ACP Visit")
        content1.setSpan(UnderlineSpan(), 0, content1.length, 0)
        txtAvgVisit.text = content1

        /* val content = SpannableString("Acers Crops Area")
         content1.setSpan(UnderlineSpan(), 0, content.length, 0)
         txtFarmers.text = content*/

        //show leads history
        leadsArray.clear()
        leadsArray.addAll(response.leads)
        var adapter = AcpLeadAdapter(this@AcpFarmerDetailsActivity, leadsArray)
        rvAcpFarmer.layoutManager =
            LinearLayoutManager(this@AcpFarmerDetailsActivity)
        rvAcpFarmer.adapter = adapter



        if (response.farmerLat != "null") {
            currentAddress(response.farmerLat.toDouble(), response.farmerLong.toDouble())
        }

        Glide.with(this)
            .load(response.farmarImage)
            .apply(RequestOptions().placeholder(R.drawable.def_user).error(R.drawable.def_user))
            .into(imgProfile)
        if (response.farmerLat != "null") {
            lat = response.farmerLat.toDouble()
            lng = response.farmerLong.toDouble()
        }
        txtNavigateTo.setOnClickListener {
            if (lat != null && lng != null) {

                val geoUri = "http://maps.google.com/maps?q=loc:$lat,$lng"
                val uri = String.format(Locale.ENGLISH, geoUri)
                val intent = Intent(Intent.ACTION_VIEW, Uri.parse(uri))
                startActivity(intent)
            } else {
                makeToast("Location is not added")
            }
        }
        val content2 = SpannableString("Activity Detail")
        content2.setSpan(UnderlineSpan(), 0, content2.length, 0)
        txtActivityDetail.text = content2

        txtActivityDetail.setOnClickListener {
            val contactNumber: String = response.contactNumber
            val farmerId: String = response.farmerId
            val intent = Intent(this@AcpFarmerDetailsActivity, TicketHistoryActivity::class.java)
            intent.putExtra("contactNumber", contactNumber)
            intent.putExtra("farmerId", farmerId)
            intent.putExtra(Constants.FARMER_FROM, "acpfarmer")
            startActivity(intent)
        }

    }


    private fun currentAddress(currentLat: Double, currentLong: Double): String? {
        val sb: StringBuilder? = null
        var address: String? = null
        try {
            var geocoder: Geocoder? = null
            val addresses: List<Address>?
            geocoder = Geocoder(this, Locale.getDefault())
            addresses = geocoder.getFromLocation(currentLat, currentLong, 1)
            if (addresses != null && !addresses.isEmpty()) {
                // Here 1 represent max location result to returned, by documents it recommended 1 to 5
                val addresssss = addresses[0]
                address = addresssss.getAddressLine(0)
                txtAddress.setText(address)

            }
        } catch (e: Exception) {

            e.printStackTrace()
        }

        return address
    }

    override fun onClick(v: View?) {
    }


}
