package com.dhanuka.indhan.acpfarmers

import android.content.Context
import android.content.Intent
import androidx.databinding.DataBindingUtil
import android.net.Uri
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.bumptech.glide.Glide
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemAcpFarmerBinding
import com.dhanuka.indhan.visithistory.VisitHistoryFarmerActivity


class ACPFarmerAdapter(var context: Context, var list: MutableList<ACPFarmerBean.Data.ACPDetail>) :
    RecyclerView.Adapter<ACPFarmerAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemAcpFarmerBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_acp_farmer, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemAcpFarmerBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {

            view.txtACPFarmerName.text = list[position].farmerName
            view.txtCropNO.text = list[position].crops[0].crop
            view.txtDDNo.text = list[position].name
            view.llayout.setOnClickListener {
                var i = Intent(context, AcpFarmerDetailsActivity::class.java)
                i.putExtra("data", list[position])
                context.startActivity(i)
            }
            view.txtVisitHistory.setOnClickListener {
                var i = Intent(context, VisitHistoryFarmerActivity::class.java)
                i.putExtra("key", list[position].farmerId)
                context.startActivity(i)
            }

            view.imgCall.setOnClickListener {
                val intent = Intent(Intent.ACTION_DIAL)
                intent.data = Uri.parse("tel: ${list[position].contactNumber}")
                context.startActivity(intent)

            }
//
            if (list[position].callStatus == "0") {
                view.imgCall.setImageDrawable(context.resources.getDrawable(R.drawable.call_ylw))


            } else {

//                view.imgCall.setOnClickListener {
//
//                }
                view.imgCall.setImageDrawable(context.resources.getDrawable(R.drawable.call_green))
            }
//
////
////            view.imgCall.setOnClickListener {
////                listenenr.onCall(position)
////            }
////
////            view.imgSetting.setOnClickListener {
////                listenenr.onChanageStatus(position)
////            }
//
//
//
//            if(list[position].leadStatus=="0"){
//                view.imgSetting.setImageDrawable(context.resources.getDrawable(R.drawable.leads_grey))
//
//                view.imgSetting.setOnClickListener {
//                    listenenr.onChanageStatus(position)
//            }
//            }else{
//                view.imgSetting.setOnClickListener {
//                    listenenr.onChanageStatus(position)
//                }
//                view.imgSetting.setImageDrawable(context.resources.getDrawable(R.drawable.leads_green))
//
//            }
//
            Glide.with(context)
                .asBitmap()
                .load(list[position].farmarImage)
//                    .centerCrop()
//                    .crossFade()
                .into(view.imgView)
        }


//


//            view.imgTestimonial.setOnClickListener {
//                val intent = Intent(context, WebViewActivity::class.java)
//                intent.putExtra("key",list[position].uploadedFile)
//                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
//                context.startActivity(intent)
//            }//
//            view.llayout.setOnClickListener {
//                val intent = Intent(context, FarmerDetailsActivity::class.java)
//                intent.putExtra("data",list[position])
//                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
//                context.startActivity(intent)
//            }


    }


}