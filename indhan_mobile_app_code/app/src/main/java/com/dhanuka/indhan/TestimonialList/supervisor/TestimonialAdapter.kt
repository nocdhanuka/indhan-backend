package com.dhanuka.indhan.TestimonialList.supervisor

import android.annotation.SuppressLint
import android.app.Dialog
import android.content.Context
import android.content.Intent
import androidx.databinding.DataBindingUtil
import android.graphics.Bitmap
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import androidx.recyclerview.widget.GridLayoutManager
import androidx.recyclerview.widget.RecyclerView
import android.view.*
import android.widget.ImageView
import android.widget.Toast
import com.bumptech.glide.Glide
import com.bumptech.glide.load.DataSource
import com.bumptech.glide.load.engine.GlideException
import com.bumptech.glide.request.RequestListener
import com.bumptech.glide.request.RequestOptions
import com.bumptech.glide.request.target.Target
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.R
import com.dhanuka.indhan.VideoPlayer.VideoPlayActivity
import com.dhanuka.indhan.databinding.ItemTestimonialSupervisorBinding
import java.util.*

class TestimonialAdapter(
    var context: Context,
    var userId: String,
    var userType: String,
    var list: MutableList<SupervisorTestimonialBean.Data.TestimonialData>,
    var listener: TestimonialListener
) : RecyclerView.Adapter<TestimonialAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemTestimonialSupervisorBinding = DataBindingUtil.inflate(
            layoutInflater,
            R.layout.item_testimonial_supervisor,
            parent,
            false
        )
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)

    }

    inner class ViewHolder(var view: ItemTestimonialSupervisorBinding) :
        RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        @SuppressLint("ClickableViewAccessibility")
        fun setData(position: Int) {
//            view.txtTestimonialName=list[position]
            view.txtTestimonialId.text = list[position].uniqueTestimonialId
            view.txtCrop.text = list[position].crop
            view.txtProduct.text = list[position].product
            view.txtCreatedAt.text = list[position].createdAt
            view.txtDescription.text = "Description :- " + list[position].description
            view.txtTestimonialName.text = list[position].testimonial
            view.txtFarmer.text = list[position].farmer
            view.txtUploader.text = list[position].uploader_name

            val adaper = TestimonialRatingAdapter(context, list[position].testimonialRating)
            view.recyclerViewRating.layoutManager =
                GridLayoutManager(context, 3)
            view.recyclerViewRating.adapter = adaper


            val requestOptions = RequestOptions()
            requestOptions.placeholder(R.drawable.video_placeholder)
            requestOptions.centerInside()

            view.imgTestimonial.scaleType = ImageView.ScaleType.CENTER_INSIDE
            view.imgTestimonial.setImageResource(R.drawable.video_placeholder)

            var containBoolean: Boolean = false
            list[position].testimonialRating.forEach {
                if (it.ecn.equals(userId)) {
                    containBoolean = true
                }
            }

            if (list[position].JuniorRated.equals("5")) {
                if (containBoolean) {
                    view.ratingBar.visibility = View.GONE
                    view.txtRateNow.visibility = View.GONE
                } else /*if (!list[position].ratedAlready){*/ {
                    view.ratingBar.visibility = View.VISIBLE
                    view.txtRateNow.visibility = View.VISIBLE
                }
            } else if (!userType.toLowerCase(Locale.getDefault()).equals("sales_area")) {
                view.ratingBar.visibility = View.GONE
                view.txtRateNow.visibility = View.GONE
            } else if (userType.equals("sales_area")) {
                if (containBoolean) {
                    view.ratingBar.visibility = View.GONE
                    view.txtRateNow.visibility = View.GONE
                } else {
                    view.ratingBar.visibility = View.VISIBLE
                    view.txtRateNow.visibility = View.VISIBLE
                }
            }

            if (list[position].uploadedFile != "") {
                view.loader.visibility = View.VISIBLE
                Glide.with(context)
                    .setDefaultRequestOptions(requestOptions)
                    .asBitmap()
                    .load(list[position].uploadedFile)
                    .listener(object : RequestListener<Bitmap> {
                        override fun onLoadFailed(
                            e: GlideException?,
                            model: Any?,
                            target: Target<Bitmap>?,
                            isFirstResource: Boolean
                        ): Boolean {
                            view.imgTestimonial.scaleType = ImageView.ScaleType.CENTER_INSIDE
                            view.imgTestimonial.setImageResource(R.drawable.video_placeholder)
                            view.loader.visibility = View.GONE
                            view.imgPlay.visibility = View.GONE
                            return false
                        }

                        override fun onResourceReady(
                            resource: Bitmap?,
                            model: Any?,
                            target: Target<Bitmap>?,
                            dataSource: DataSource?,
                            isFirstResource: Boolean
                        ): Boolean {
                            if (resource != null) {
                                view.loader.visibility = View.GONE
                                view.imgPlay.visibility = View.VISIBLE
                                view.imgTestimonial.scaleType = ImageView.ScaleType.CENTER_CROP
                                view.imgTestimonial.setImageBitmap(resource)
                            } else {
                                view.loader.visibility = View.GONE
                                view.imgTestimonial.scaleType = ImageView.ScaleType.CENTER_INSIDE
                                view.imgTestimonial.setImageResource(R.drawable.video_placeholder)
                                view.imgPlay.visibility = View.GONE
                            }
                            return true
                        }
                    })
                    .into(view.imgTestimonial)
            } else {
                view.loader.visibility = View.GONE
                view.imgPlay.visibility = View.GONE
            }


            view.ratingBar.setIsIndicator(false)

            view.ratingBar.setOnRatingBarChangeListener { ratingBar, fl, b ->
                var containBoolean: Boolean = false
                list[position].testimonialRating.forEach {
                    if (it.supervisorId.equals(userId)) {
                        containBoolean = true
                    }
                }
                if (containBoolean) {
                    view.ratingBar.rating = 0f
                    Toast.makeText(
                        context,
                        "You have already rated this testimonial.",
                        Toast.LENGTH_SHORT
                    ).show()
                } else if (!fl.toString().equals("0.0"))/*if (!list[position].ratedAlready){*/ {
                    showdialog(position, fl, view)
                }
            }

            view.ratingBar.setOnTouchListener(object : View.OnTouchListener {
                private var downXValue: Float = 0.toFloat()
                override fun onTouch(v: View?, event: MotionEvent): Boolean {
                    if (event.action === MotionEvent.ACTION_DOWN) {
                        downXValue = event.x
                        return false
                    }

                    if (event.action === MotionEvent.ACTION_MOVE) {
                        // When true is returned, view will not handle this event.
                        return true
                    }

                    if (event.action === MotionEvent.ACTION_UP) {
                        val currentX = event.x
                        var difference = 0f
                        // Swipe on left side
                        if (currentX < downXValue)
                            difference = downXValue - currentX
                        else if (currentX > downXValue)
                            difference = currentX - downXValue// Swipe on right side

                        return difference >= 10

                    }
                    return false
                }
            })

            view.imgTestimonial.setOnClickListener {
                /*val intent = Intent(context, WebViewActivity::class.java)
                intent.putExtra("key", list[position].uploadedFile)
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
                context.startActivity(intent)*/

                val intent = Intent(context, VideoPlayActivity::class.java)
                intent.putExtra("VideoUrl", list[position].uploadedFile)
                context.startActivity(intent)
            }

        }

    }

    private fun showdialog(postion: Int, fl: Float, view: ItemTestimonialSupervisorBinding) {
        val dialog = Dialog(context)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_confirmation)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        val tvNo = dialog.findViewById<View>(R.id.tvMeCancel) as Medium
        val msg = dialog.findViewById<View>(R.id.msg) as Medium

        msg.text =
            context.getString(R.string.sure_rate) + fl.toString() + context.getString(R.string.startest)

        tvNo.setOnClickListener {
            list[postion].rated = "0"
            view.ratingBar.rating = 0f
            dialog.dismiss()
        }

        tvOkay.setOnClickListener {
            listener.onRating(list[postion].selfParticipationId, fl.toString())
            /*list[postion].rated = fl.toString()
            list[postion].ratedAlready = true*/
            dialog.dismiss()
        }
        dialog.show()
    }

}