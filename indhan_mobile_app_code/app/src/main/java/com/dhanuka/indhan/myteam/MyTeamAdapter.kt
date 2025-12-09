package com.dhanuka.indhan.myteam

import android.content.Context
import android.content.Intent
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.bumptech.glide.Glide
import com.dhanuka.indhan.R
import com.dhanuka.indhan.TestimonialList.filter.DDBean
import com.dhanuka.indhan.databinding.ItemMyTeamBinding
import com.dhanuka.indhan.myteam.editdd.EditDDActivity


class MyTeamAdapter(var context: Context, var list: MutableList<DDBean.Data>) :
    RecyclerView.Adapter<MyTeamAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemMyTeamBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_my_team, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemMyTeamBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
            view.txtFarmerName.text = list[position].name
            view.txtNoOfVillage.text = list[position].villagesCount.toString() + " Villages"
            view.txtNoOfCrop.text = list[position].cropCount.toString() + " Crops"
            view.txtAvgNo.text = list[position].averageFarmer.toString()

            if (list[position].ddImage != "") {
                Glide.with(context)
                    .load(list[position].ddImage)
//                    .centerCrop()
//                    .crossFade()
                    .into(view.imgFarmer)
            } else {

            }

            view.imgEdit.setOnClickListener {


                var intent = Intent(context, EditDDActivity::class.java)
                intent.putExtra("data", list[position])
                context.startActivity(intent)

            }

            view.llayout.setOnClickListener {
                var intent = Intent(context, MyTeamDetailsActivity::class.java)
                intent.putExtra("data", list[position])
                context.startActivity(intent)

            }


        }

    }


}