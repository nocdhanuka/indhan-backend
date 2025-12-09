package com.dhanuka.indhan.FarmersDetails

import android.app.Dialog
import android.content.Intent
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.location.Address
import android.location.Geocoder
import android.net.Uri
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import android.text.SpannableString
import android.text.style.UnderlineSpan
import android.view.View
import android.view.Window
import com.bumptech.glide.Glide
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.R
import com.dhanuka.indhan.farmerlist.FarmerBean
import com.dhanuka.indhan.visithistory.VisitHistoryFarmerActivity
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_farmer_details.*
import kotlinx.android.synthetic.main.ticket_history_row.view.*
import java.util.*

class FarmerDetailsActivity : AppCompatActivity() {


    private lateinit var response: FarmerBean.VillageFarmerData
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_farmer_details)
        response = Gson().fromJson<FarmerBean.VillageFarmerData>(
            intent.getStringExtra("data")?.toString(),
            FarmerBean.VillageFarmerData::class.java
        )
        setView(response)

        txtCallNow.setOnClickListener {
            val intent = Intent(Intent.ACTION_DIAL)
            intent.data = Uri.parse("tel: ${response.contactNumber}")
            startActivity(intent)
        }



        txtNoOfAvgVisit.setOnClickListener {
            val i = Intent(this, VisitHistoryFarmerActivity::class.java)
            i.putExtra("key", response.farmerId)
            startActivity(i)


        }


        txtAvgVisit.setOnClickListener {
            val i = Intent(this, VisitHistoryFarmerActivity::class.java)
            i.putExtra("key", response.farmerId)
            startActivity(i)

        }

        txtNoFarmer.setOnClickListener {
            showdialogTwo()
        }
        txtFarmers.setOnClickListener {
            showdialogTwo()
        }


    }


    private fun setView(response: FarmerBean.VillageFarmerData) {
        val newName = response.contactNumber.substring(0, 6) + "XXXX"

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

        val content = SpannableString("Acers Crops Area")
        content.setSpan(UnderlineSpan(), 0, content.length, 0)
        txtFarmers.text = content

        if (response.farmerLat != "null" && response.farmerLat != "") {
            currentAddress(response.farmerLat.toDouble(), response.farmerLong.toDouble())
        }

        Glide.with(this)
            .load(response.farmarImage)
            .apply(RequestOptions().placeholder(R.drawable.def_user).error(R.drawable.def_user))

//                    .centerCrop()
//                    .crossFade()
            .into(imgProfile)
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


    private fun showdialogTwo() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_earning)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(true)
        val adapter = AcresAdapter(this@FarmerDetailsActivity, response.products)
        val recyclerView = dialog.findViewById<View>(R.id.recyclerView) as RecyclerView
        recyclerView.layoutManager =
            LinearLayoutManager(this@FarmerDetailsActivity)
        recyclerView.adapter = adapter


        dialog.show()
    }

}
