package com.dhanuka.indhan.myteam

import android.content.Context
import android.content.Intent
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.VillageDetails.VillageDetailsActivity
import com.dhanuka.indhan.databinding.ItemVillageCropBinding


class VillagesHistoryAdapter(
    var context: Context,
    var list: MutableList<DDProfileDetails.Data.MyVillage>
) : RecyclerView.Adapter<VillagesHistoryAdapter.ViewHolder>() {

    var layoutInflater: LayoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemVillageCropBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_village_crop, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {

        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemVillageCropBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
            view.txtCrop.text = list[position].village
            view.txtNoOfAcres.visibility = View.GONE
            view.txtFarmers.text = list[position].created_at
            view.llayout.setOnClickListener {
                var i: Intent = Intent(context, VillageDetailsActivity::class.java)
                i.putExtra("villageId", list[position].village_id)
                context.startActivity(i)
            }


        }


    }
}