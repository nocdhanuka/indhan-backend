package com.dhanuka.indhan.ATPMenu.activity

import android.content.Context
import android.content.Intent
import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.ATPMenu.activity.activities.ActivateAtpActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemAtpActiveBinding
import java.io.Serializable

class ATPListAdapter(
    var context: Context,
    var list: MutableList<ATPBean.Data>,
    var listener: OnClickListener
) : RecyclerView.Adapter<ATPListAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemAtpActiveBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_atp_active, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.setData(position)
    }


    inner class ViewHolder(var view: ItemAtpActiveBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
            view.llShowDetail.setOnClickListener {
                var intent = Intent(context, ActivateAtpActivity::class.java)
                intent.putExtra(Constants.ATP_DETAIL, list[position] as Serializable)
                context.startActivity(intent)

            }

            view.txtATPName.text = "ATP: " + list[position].plan_atp_id
            if (list[position].status == "1") {
                view.txtStatus.text = "Active"


            } else {
                view.txtStatus.text = ""

                /* view.txtStatus.text = "Activate"
                 view.txtStatus.setOnClickListener {
                     for (i in 1 until list.size) {
                         if (list[position].status == "1") {

                         }
                     }

                 }*/

            }


//            view.imgTestimonial.visibility= View.GONE

//            //view.imgTestimonial.setImageBitmap(retriveVideoFrameFromVideo(list[position].uploadedFile))
//
//
//            if(list[position].uploadedFile!=""){
//            Glide.with(context)
//                    .asBitmap()
//                    .load(list[position].uploadedFile)
////                    .centerCrop()
////                    .crossFade()
//                    .into(view.imgTestimonial)
//            }else{
//
//            }
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


}