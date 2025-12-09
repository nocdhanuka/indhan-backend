package com.dhanuka.indhan.validatefarmers

import android.content.Context
import androidx.recyclerview.widget.RecyclerView
import android.text.TextUtils
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.bumptech.glide.Glide
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.Activity.RegisterFarmersMVP.Model.IfcFarmersListResponse
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_validate_ifc_farmer.view.*
import java.io.File
import java.util.*

class ValidateIfcFarmerAdapter(
    private val context: Context,
    private val listener: IfcValidationListener,
    private val ifcFarmerDataList: ArrayList<IfcFarmersListResponse.Data>,
    private val baseUrl: String
) :
    RecyclerView.Adapter<ValidateIfcFarmerAdapter.MyViewHolder>() {
    init {

    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewTYpe: Int
    ): ValidateIfcFarmerAdapter.MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_validate_ifc_farmer, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: ValidateIfcFarmerAdapter.MyViewHolder, position: Int) {
        holder.itemView.txtVillageName.text = ifcFarmerDataList[position].farmer_name

        if (!ifcFarmerDataList[position].mobile.equals("")) {
            holder.itemView.txtNoOfFarmers.text = ifcFarmerDataList[position].mobile.replaceFirst(
                ifcFarmerDataList[position].mobile.substring(0, 4),
                "****"
            )
        }

        if (!TextUtils.isEmpty(ifcFarmerDataList[position].image)) {
            val requestOptions = RequestOptions()
            requestOptions.placeholder(R.drawable.name)
            requestOptions.error(R.drawable.name)
            if (ifcFarmerDataList[position].image!!.contains("https://")) {
                Glide.with(context)
                    .setDefaultRequestOptions(requestOptions)
                    .load(baseUrl.plus(ifcFarmerDataList[position].image))
                    .into(holder.itemView.ivFarmerImage)
            } else {
                Glide.with(context)
                    .setDefaultRequestOptions(requestOptions)
                    .load(File(ifcFarmerDataList[position].image))
                    .into(holder.itemView.ivFarmerImage)
            }
        }


//        if (ifcFarmerDataList[position].callStatus == "0") {
//            holder.itemView.imgCall.setImageDrawable(context.resources.getDrawable(R.drawable.call_ylw))
//        } else {
//            holder.itemView.imgCall.setImageDrawable(context.resources.getDrawable(R.drawable.call_green))
//        }
//
        holder.itemView.imgCall.setImageDrawable(context.resources.getDrawable(R.drawable.call_green))

        holder.itemView.imgSetting.setImageDrawable(context.resources.getDrawable(R.drawable.leads_grey))
//        if (ifcFarmerDataList[position].leadStatus == "0") {
//            holder.itemView.imgSetting.setImageDrawable(context.resources.getDrawable(R.drawable.leads_grey))
//        } else {
//            holder.itemView.imgSetting.setImageDrawable(context.resources.getDrawable(R.drawable.leads_green))
//        }


        holder.itemView.imgCall.setOnClickListener {
            listener.onIfcCall(position)
        }

        holder.itemView.imgSetting.setOnClickListener {
            listener.onChangeIfcStatus(position)
        }
    }

    override fun getItemCount(): Int {
        return ifcFarmerDataList.size
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {

    }
}