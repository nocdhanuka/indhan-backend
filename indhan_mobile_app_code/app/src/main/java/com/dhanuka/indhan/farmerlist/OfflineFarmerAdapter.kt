package com.dhanuka.indhan.farmerlist

import android.content.Context
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.bumptech.glide.Glide
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.R
import com.dhanuka.indhan.addfarmers.model.AddFarmerModel
import com.dhanuka.indhan.databinding.ItemFarmerBinding


class OfflineFarmerAdapter(var context: Context, var list: MutableList<AddFarmerModel>) :
    RecyclerView.Adapter<OfflineFarmerAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemFarmerBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_farmer, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemFarmerBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
            view.txtVillageName.text = list[position].farmerName
            view.txtNoOfFarmers.text = list[position].fatherName
            view.txtAvgVisit.text = "${list[position].acres} Acres crop area"
            Glide.with(context)
                .load(list[position].filePath)
                .apply(RequestOptions().placeholder(R.drawable.def_user).error(R.drawable.def_user))
                .into(view.imgFarmer)
        }
    }
}